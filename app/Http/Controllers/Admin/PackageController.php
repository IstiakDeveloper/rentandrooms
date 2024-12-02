<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Amenity;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\EntireProperty;
use App\Models\Maintain;
use App\Models\Package;
use App\Models\Property;
use App\Models\RoomPrice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;


class PackageController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        // Get initial data based on user role
        $initialData = $this->getInitialData($user);

        return Inertia::render('Admin/Packages/Create', $initialData);
    }

    private function getInitialData($user)
    {
        $isSuperAdmin = $user->roles->pluck('name')->contains('Super Admin');

        return [
            'countries' => Country::all(),
            'properties' => $isSuperAdmin ? Property::all() : Property::where('user_id', $user->id)->get(),
            'maintains' => $isSuperAdmin ? Maintain::all() : Maintain::where('user_id', $user->id)->get(),
            'amenities' => $isSuperAdmin ? Amenity::all() : Amenity::where('user_id', $user->id)->get(),
            'initialRoom' => [
                'name' => '',
                'number_of_beds' => 0,
                'number_of_bathrooms' => 0,
                'prices' => [
                    [
                        'type' => '',
                        'fixed_price' => 0,
                        'discount_price' => null,
                        'booking_price' => 0
                    ]
                ]
            ],
            'initialEntireProperty' => [
                'prices' => [
                    [
                        'type' => '',
                        'fixed_price' => 0,
                        'discount_price' => null,
                        'booking_price' => 0
                    ]
                ]
            ]
        ];
    }

    public function getCities($countryId)
    {
        $cities = City::where('country_id', $countryId)->get();
        return response()->json($cities);
    }

    public function getAreas($cityId)
    {
        $areas = Area::where('city_id', $cityId)->get();
        return response()->json($areas);
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
            'rooms' => 'array',
            'rooms.*.name' => 'required|string',
            'rooms.*.number_of_beds' => 'required|integer',
            'rooms.*.number_of_bathrooms' => 'required|integer',
            'rooms.*.prices' => 'array',
            'rooms.*.prices.*.type' => 'required|in:Day,Week,Month',
            'rooms.*.prices.*.fixed_price' => 'required|numeric',
            'rooms.*.prices.*.discount_price' => 'nullable|numeric',
            'rooms.*.prices.*.booking_price' => 'required|numeric',
            'paidMaintains' => 'array',
            'paidMaintains.*.maintain_id' => 'required|exists:maintains,id',
            'paidMaintains.*.price' => 'required|numeric',
            'paidAmenities' => 'array',
            'paidAmenities.*.amenity_id' => 'required|exists:amenities,id',
            'paidAmenities.*.price' => 'required|numeric',
            'photos' => 'array',
            'photos.*' => 'image|max:1024',
            'entireProperty' => 'array',
            'entireProperty.prices' => 'array',
            'entireProperty.prices.*.type' => 'required_if:selection,entire|in:Day,Week,Month',
            'entireProperty.prices.*.fixed_price' => 'required_if:selection,entire|numeric',
            'entireProperty.prices.*.discount_price' => 'nullable|numeric',
            'entireProperty.prices.*.booking_price' => 'required_if:selection,entire|numeric',
            'video_link' => 'nullable|url',
            'expiration_date' => 'required|date|after:today',
            'selection' => 'required|in:entire,rooms',
            'freeMaintains' => 'array',
            'freeMaintains.*' => 'exists:maintains,id',
            'freeAmenities' => 'array',
            'freeAmenities.*' => 'exists:amenities,id',
        ]);

        try {
            \DB::beginTransaction();

            // Create package
            $package = $this->createPackage($validated);

            // Handle selection type (entire property or rooms)
            if ($validated['selection'] === 'entire') {
                $this->handleEntireProperty($package, $validated['entireProperty']);
            } else {
                $this->handleRooms($package, $validated['rooms']);
            }

            // Handle maintains and amenities
            $this->handleMaintainsAndAmenities($package, $validated);

            // Handle photo uploads
            if ($request->hasFile('photos')) {
                $this->handlePhotos($package, $request->file('photos'));
            }

            \DB::commit();

            return redirect()->route('admin.packages.index')
                ->with('success', 'Package created successfully.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Error creating package: ' . $e->getMessage());
        }
    }

    private function createPackage($data)
    {
        $status = strtotime($data['expiration_date']) <= strtotime(now()) ? 'expired' : 'active';

        return Package::create([
            'country_id' => $data['country_id'],
            'city_id' => $data['city_id'],
            'area_id' => $data['area_id'],
            'property_id' => $data['property_id'],
            'name' => $data['name'],
            'address' => $data['address'],
            'map_link' => $data['map_link'],
            'number_of_kitchens' => $data['number_of_kitchens'],
            'number_of_rooms' => $data['number_of_rooms'],
            'common_bathrooms' => $data['common_bathrooms'],
            'seating' => $data['seating'],
            'details' => $data['details'],
            'video_link' => $data['video_link'],
            'expiration_date' => $data['expiration_date'],
            'status' => $status,
            'user_id' => Auth::id(),
        ]);
    }

    private function handleEntireProperty($package, $entirePropertyData)
    {
        $entireProperty = EntireProperty::create([
            'user_id' => Auth::id(),
            'package_id' => $package->id,
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

    private function handleRooms($package, $roomsData)
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

    private function handleMaintainsAndAmenities($package, $data)
    {
        // Handle free maintains
        if (!empty($data['freeMaintains'])) {
            foreach ($data['freeMaintains'] as $maintainId) {
                $package->maintains()->attach($maintainId, [
                    'is_paid' => false,
                    'user_id' => Auth::id(),
                ]);
            }
        }

        // Handle free amenities
        if (!empty($data['freeAmenities'])) {
            foreach ($data['freeAmenities'] as $amenityId) {
                $package->amenities()->attach($amenityId, [
                    'is_paid' => false,
                    'user_id' => Auth::id(),
                ]);
            }
        }

        // Handle paid maintains
        if (!empty($data['paidMaintains'])) {
            foreach ($data['paidMaintains'] as $maintainData) {
                $package->maintains()->attach($maintainData['maintain_id'], [
                    'is_paid' => true,
                    'price' => $maintainData['price'],
                    'user_id' => Auth::id(),
                ]);
            }
        }

        // Handle paid amenities
        if (!empty($data['paidAmenities'])) {
            foreach ($data['paidAmenities'] as $amenityData) {
                $package->amenities()->attach($amenityData['amenity_id'], [
                    'is_paid' => true,
                    'price' => $amenityData['price'],
                    'user_id' => Auth::id(),
                ]);
            }
        }
    }

    private function handlePhotos($package, $photos)
    {
        foreach ($photos as $photo) {
            $path = $photo->store('photos', 'public');
            $package->photos()->create([
                'url' => $path,
                'user_id' => Auth::id(),
            ]);
        }
    }
}
