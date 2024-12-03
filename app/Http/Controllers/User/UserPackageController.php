<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserPackageController extends Controller
{
    public function index(Request $request)
    {
        $query = Package::query()
            ->with([
                'country:id,name',
                'city:id,name',
                'area:id,name',
                'property:id,name',
                'photos', // Remove specific column selection
                'maintains:id,name',
                'amenities:id,name'
            ])
            // Only show active packages
            ->where('status', 'active')
            // Ensure package hasn't expired
            ->where('expiration_date', '>', now());

        // Filter by country
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by area
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        // Filter by number of rooms
        if ($request->filled('rooms')) {
            $query->where('number_of_rooms', $request->rooms);
        }

        // Search by name or address
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $packages = $query->paginate(12)
            ->withQueryString()
            ->through(function ($package) {
                // Dynamically get the first photo's path
                $firstPhoto = $package->photos->first();

                return [
                    'id' => $package->id,
                    'name' => $package->name,
                    'address' => $package->address,
                    'number_of_rooms' => $package->number_of_rooms,
                    'country' => $package->country?->name,
                    'city' => $package->city?->name,
                    'area' => $package->area?->name,
                    'property_type' => $package->property?->name,
                    // Flexible photo handling
                    'main_photo' => $firstPhoto
                        ? ($firstPhoto->url ?? $firstPhoto->path ?? $firstPhoto->filename ?? null)
                        : null,
                    'maintains' => $package->maintains->pluck('name'),
                    'amenities' => $package->amenities->pluck('name'),
                    'price' => $package->price ?? null,
                ];
            });

        // Get filter options
        $filterOptions = [
            'countries' => \App\Models\Country::all(['id', 'name']),
            'cities' => \App\Models\City::all(['id', 'name']),
            'areas' => \App\Models\Area::all(['id', 'name']),
            'room_counts' => Package::distinct()->pluck('number_of_rooms'),
        ];

        return Inertia::render('User/Packages/Index', [
            'packages' => $packages,
            'filters' => $request->all(),
            'filterOptions' => $filterOptions,
        ]);
    }


    public function show($id)
    {
        $package = Package::with([
            'country',
            'city',
            'area',
            'property',
            'rooms' => function($query) {
                $query->with([
                    'roomPrices'
                ]);
            },
            'packageMaintains' => function($query) {
                $query->with('maintain.maintainType');
            },
            'packageAmenities' => function($query) {
                $query->with('amenity.amenityType');
            },
            'photos'
        ])->findOrFail($id);

        return Inertia::render('User/Packages/Show', [
            'package' => $package,
            'relatedPackages' => Package::where('city_id', $package->city_id)
                ->where('id', '!=', $package->id)
                ->limit(4)
                ->get()
        ]);
    }



}
