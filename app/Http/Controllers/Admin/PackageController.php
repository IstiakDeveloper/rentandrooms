<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\EntireProperty;
use App\Models\Maintain;
use App\Models\Package;
use App\Models\Property;
use App\Models\RoomPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PackageController extends Controller
{
    /**
     * Display the create package page.
     */
    public function create()
    {
        $user = Auth::user();

        // Load all the required data
        $countries = Country::all();
        $properties = Property::when(!$user->hasRole('Super Admin'), function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        })->get();

        $maintains = Maintain::when(!$user->hasRole('Super Admin'), function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        })->get();

        $amenities = Amenity::when(!$user->hasRole('Super Admin'), function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        })->get();

        return Inertia::render('Admin/Packages/Create', [
            'countries' => $countries,
            'properties' => $properties,
            'maintains' => $maintains,
            'amenities' => $amenities,
        ]);
    }

    /**
     * Store a new package in the database.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'property_id' => 'required|exists:properties,id',
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'map_link' => 'nullable|url',
            'number_of_kitchens' => 'required|integer|min:0',
            'number_of_rooms' => 'required|integer|min:0',
            'common_bathrooms' => 'required|integer|min:0',
            'seating' => 'required|integer|min:0',
            'details' => 'nullable|string',
            'video_link' => 'nullable|url',
            'expiration_date' => 'required|date|after:today',
            'selection' => 'required|in:entire,rooms',
            'entireProperty.prices.*.type' => 'required|in:Day,Week,Month',
            'entireProperty.prices.*.fixed_price' => 'required|numeric',
            'entireProperty.prices.*.discount_price' => 'nullable|numeric',
            'entireProperty.prices.*.booking_price' => 'required|numeric',
            'rooms' => 'nullable|array',
            'rooms.*.name' => 'required|string',
            'rooms.*.number_of_beds' => 'required|integer',
            'rooms.*.number_of_bathrooms' => 'required|integer',
            'rooms.*.prices' => 'required|array',
            'rooms.*.prices.*.type' => 'required|in:Day,Week,Month',
            'rooms.*.prices.*.fixed_price' => 'required|numeric',
            'rooms.*.prices.*.discount_price' => 'nullable|numeric',
            'rooms.*.prices.*.booking_price' => 'required|numeric',
            'freeMaintains' => 'array',
            'freeMaintains.*' => 'exists:maintains,id',
            'freeAmenities' => 'array',
            'freeAmenities.*' => 'exists:amenities,id',
            'paidMaintains' => 'array',
            'paidMaintains.*.maintain_id' => 'required|exists:maintains,id',
            'paidMaintains.*.price' => 'required|numeric',
            'paidAmenities' => 'array',
            'paidAmenities.*.amenity_id' => 'required|exists:amenities,id',
            'paidAmenities.*.price' => 'required|numeric',
            'photos.*' => 'nullable|image|max:2048',
        ]);

        $status = 'active';
        if (strtotime($request->expiration_date) <= strtotime(now())) {
            $status = 'expired';
        }

        // Create the package
        $package = Package::create([
            'country_id' => $validated['country_id'],
            'city_id' => $validated['city_id'],
            'area_id' => $validated['area_id'],
            'property_id' => $validated['property_id'],
            'name' => $validated['name'],
            'address' => $validated['address'],
            'map_link' => $validated['map_link'],
            'number_of_kitchens' => $validated['number_of_kitchens'],
            'number_of_rooms' => $validated['number_of_rooms'],
            'common_bathrooms' => $validated['common_bathrooms'],
            'seating' => $validated['seating'],
            'details' => $validated['details'],
            'video_link' => $validated['video_link'],
            'expiration_date' => $validated['expiration_date'],
            'status' => $status,
            'user_id' => Auth::id(),
        ]);

        // Handle storing prices for either entire property or rooms
        if ($request->selection === 'entire') {
            $entireProperty = EntireProperty::create([
                'package_id' => $package->id,
                'user_id' => Auth::id(),
            ]);

            foreach ($validated['entireProperty']['prices'] as $price) {
                RoomPrice::create([
                    'entire_property_id' => $entireProperty->id,
                    'type' => $price['type'],
                    'fixed_price' => $price['fixed_price'],
                    'discount_price' => $price['discount_price'],
                    'booking_price' => $price['booking_price'],
                    'user_id' => Auth::id(),
                ]);
            }
        } else {
            foreach ($validated['rooms'] as $room) {
                $roomModel = $package->rooms()->create([
                    'name' => $room['name'],
                    'number_of_beds' => $room['number_of_beds'],
                    'number_of_bathrooms' => $room['number_of_bathrooms'],
                    'user_id' => Auth::id(),
                ]);

                foreach ($room['prices'] as $price) {
                    $roomModel->prices()->create([
                        'type' => $price['type'],
                        'fixed_price' => $price['fixed_price'],
                        'discount_price' => $price['discount_price'],
                        'booking_price' => $price['booking_price'],
                        'user_id' => Auth::id(),
                    ]);
                }
            }
        }

        // Attach amenities and maintains
        if ($request->freeMaintains) {
            $package->maintains()->syncWithPivotValues($request->freeMaintains, ['is_paid' => false, 'user_id' => Auth::id()]);
        }

        if ($request->freeAmenities) {
            $package->amenities()->syncWithPivotValues($request->freeAmenities, ['is_paid' => false, 'user_id' => Auth::id()]);
        }

        foreach ($request->paidMaintains as $maintain) {
            $package->maintains()->attach($maintain['maintain_id'], [
                'is_paid' => true,
                'price' => $maintain['price'],
                'user_id' => Auth::id(),
            ]);
        }

        foreach ($request->paidAmenities as $amenity) {
            $package->amenities()->attach($amenity['amenity_id'], [
                'is_paid' => true,
                'price' => $amenity['price'],
                'user_id' => Auth::id(),
            ]);
        }

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('photos', 'public');
                $package->photos()->create([
                    'url' => $path,
                    'user_id' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('admin.packages.index')->with('message', 'Package created successfully.');
    }
}
