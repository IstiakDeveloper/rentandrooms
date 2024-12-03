<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Amenity, Area, City, Country, EntireProperty, Maintain, Package, Photo, Property, RoomPrice};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $packages = Package::with([
            'country',
            'city',
            'area',
            'property',
            'rooms.prices',
            'maintains',
            'amenities',
            'entireProperty.prices',
            'photos'
        ])->when(!$user->hasRole('Super Admin'), function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        })->get();

        return Inertia::render('Admin/Packages/Index', [
            'packages' => $packages
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        $data = [
            'countries' => Country::with('cities.areas')->get(),
            'properties' => Property::when(!$user->hasRole('Super Admin'), function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })->get(),
            'maintains' => Maintain::when(!$user->hasRole('Super Admin'), function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })->get(),
            'amenities' => Amenity::when(!$user->hasRole('Super Admin'), function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })->get(),
        ];

        return Inertia::render('Admin/Packages/Create', $data);
    }

    public function getCities($countryId)
    {
        return City::where('country_id', $countryId)->get();
    }

    public function getAreas($cityId)
    {
        return Area::where('city_id', $cityId)->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required',
            'city_id' => 'required',
            'area_id' => 'required',
            'property_id' => 'required',
            'name' => 'required|string',
            'address' => 'required|string',
            'map_link' => 'nullable|string',
            'number_of_kitchens' => 'required|integer',
            'number_of_rooms' => 'required|integer',
            'common_bathrooms' => 'required|integer',
            'seating' => 'required|integer',
            'details' => 'nullable|string',
            'video_link' => 'nullable|url',
            'expiration_date' => 'required|date|after:today',
            'selection' => 'required|in:entire,rooms',
            'rooms' => 'required_if:selection,rooms|array',
            'rooms.*.name' => 'required_if:selection,rooms|string',
            'rooms.*.number_of_beds' => 'required_if:selection,rooms|integer',
            'rooms.*.number_of_bathrooms' => 'required_if:selection,rooms|integer',
            'rooms.*.prices' => 'required_if:selection,rooms|array',
            'rooms.*.prices.*.type' => 'required_if:selection,rooms|in:Day,Week,Month',
            'rooms.*.prices.*.fixed_price' => 'required_if:selection,rooms|numeric',
            'rooms.*.prices.*.discount_price' => 'nullable|numeric',
            'rooms.*.prices.*.booking_price' => 'required_if:selection,rooms|numeric',
            'entireProperty' => 'required_if:selection,entire|array',
            'entireProperty.prices' => 'required_if:selection,entire|array',
            'entireProperty.prices.*.type' => 'required_if:selection,entire|in:Day,Week,Month',
            'entireProperty.prices.*.fixed_price' => 'required_if:selection,entire|numeric',
            'entireProperty.prices.*.discount_price' => 'nullable|numeric',
            'entireProperty.prices.*.booking_price' => 'required_if:selection,entire|numeric',
            'freeMaintains' => 'array',
            'freeAmenities' => 'array',


        ]);

        // dd($request->all(), $request->file('photos'));
        $package = Package::create([
            ...$validated,
            'status' => strtotime($validated['expiration_date']) <= strtotime(now()) ? 'expired' : 'active',
            'user_id' => Auth::id(),
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                // Debug the photo contents
                dd($photo);
            }
        }

        if ($validated['selection'] === 'entire') {
            $this->handleEntirePropertyCreation($package, $validated['entireProperty']);
        } else {
            $this->handleRoomsCreation($package, $validated['rooms']);
        }

        $this->handleMaintainsAndAmenities($package, $validated);
        $this->handlePhotos($package, $request);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package created successfully.');
    }

    public function edit(Package $package)
    {
        $package->load([
            'rooms.prices',
            'maintains',
            'amenities',
            'entireProperty.prices',
            'photos'
        ]);

        return Inertia::render('Admin/Packages/Edit', [
            'package' => $package,
            'countries' => Country::all(),
            'cities' => City::where('country_id', $package->country_id)->get(),
            'areas' => Area::where('city_id', $package->city_id)->get(),
            'properties' => Property::where('user_id', Auth::id())->get(),
            'maintains' => Maintain::where('user_id', Auth::id())->get(),
            'amenities' => Amenity::where('user_id', Auth::id())->get(),
        ]);
    }

    public function update(Request $request, Package $package)
    {
        // Similar validation as store method
        $validated = $request->validate([
            // ... same validation rules as store
        ]);

        $package->update([
            ...$validated,
            'status' => strtotime($validated['expiration_date']) <= strtotime(now()) ? 'expired' : 'active',
        ]);

        if ($validated['selection'] === 'entire') {
            $this->handleEntirePropertyUpdate($package, $validated['entireProperty']);
        } else {
            $this->handleRoomsUpdate($package, $validated['rooms']);
        }

        $this->handleMaintainsAndAmenities($package, $validated, true);
        $this->handlePhotos($package, $request);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('admin.packages.index')
            ->with('success', 'Package deleted successfully.');
    }

    private function handleEntirePropertyCreation($package, $entirePropertyData)
    {
        $entireProperty = EntireProperty::create([
            'package_id' => $package->id,
            'user_id' => Auth::id(),
        ]);

        foreach ($entirePropertyData['prices'] as $priceData) {
            RoomPrice::create([
                'entire_property_id' => $entireProperty->id,
                'type' => $priceData['type'],
                'fixed_price' => $priceData['fixed_price'],
                'discount_price' => $priceData['discount_price'],
                'booking_price' => $priceData['booking_price'],
                'user_id' => Auth::id(),
            ]);
        }
    }

    private function handleRoomsCreation($package, $roomsData)
    {
        foreach ($roomsData as $roomData) {
            $room = $package->rooms()->create([
                'name' => $roomData['name'],
                'number_of_beds' => $roomData['number_of_beds'],
                'number_of_bathrooms' => $roomData['number_of_bathrooms'],
                'user_id' => Auth::id(),
            ]);

            foreach ($roomData['prices'] as $priceData) {
                $room->prices()->create([
                    'type' => $priceData['type'],
                    'fixed_price' => $priceData['fixed_price'],
                    'discount_price' => $priceData['discount_price'],
                    'booking_price' => $priceData['booking_price'],
                    'user_id' => Auth::id(),
                ]);
            }
        }
    }

    private function handleMaintainsAndAmenities($package, $data, $isUpdate = false)
    {
        if ($isUpdate) {
            $package->maintains()->detach();
            $package->amenities()->detach();
        }

        // Handle free maintains and amenities
        foreach ($data['freeMaintains'] ?? [] as $maintainId) {
            $package->maintains()->attach($maintainId, [
                'is_paid' => false,
                'user_id' => Auth::id(),
            ]);
        }

        foreach ($data['freeAmenities'] ?? [] as $amenityId) {
            $package->amenities()->attach($amenityId, [
                'is_paid' => false,
                'user_id' => Auth::id(),
            ]);
        }

        // Handle paid maintains and amenities
        foreach ($data['paidMaintains'] ?? [] as $maintainData) {
            $package->maintains()->attach($maintainData['maintain_id'], [
                'is_paid' => true,
                'price' => $maintainData['price'],
                'user_id' => Auth::id(),
            ]);
        }

        foreach ($data['paidAmenities'] ?? [] as $amenityData) {
            $package->amenities()->attach($amenityData['amenity_id'], [
                'is_paid' => true,
                'price' => $amenityData['price'],
                'user_id' => Auth::id(),
            ]);
        }
    }

    private function handlePhotos($package, $request)
    {
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('photos', 'public');
                $package->photos()->create([
                    'url' => $path,
                    'user_id' => Auth::id(),
                ]);
            }
        }
    }
}
