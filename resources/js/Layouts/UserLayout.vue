<template>
    <div :class="theme">
      <!-- Navigation Bar -->
      <nav class="bg-white dark:bg-gray-900 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
              <Link href="/" class="flex items-center">
                <img
                  src="/path-to-your-logo.svg"
                  alt="Rent & Rooms Logo"
                  class="h-10 w-auto"
                />
                <span class="ml-3 text-xl font-bold text-gray-800 dark:text-gray-200">
                  Rent & Rooms
                </span>
              </Link>
            </div>

            <!-- Country Selector and Contact -->
            <div class="flex items-center space-x-6">
              <!-- Country Selector -->
              <div class="relative">
                <button
                  @click="toggleCountryDropdown"
                  class="flex items-center text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400"
                >
                  <img
                    :src="selectedCountry.flag"
                    alt="Country Flag"
                    class="h-5 w-5 mr-2 rounded-full"
                  />
                  <span>{{ selectedCountry.name }}</span>
                  <svg
                    class="ml-1 h-4 w-4"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
                <div
                  v-if="countryDropdownOpen"
                  class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-20"
                >
                  <div
                    v-for="country in countries"
                    :key="country.code"
                    @click="selectCountry(country)"
                    class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer flex items-center"
                  >
                    <img
                      :src="country.flag"
                      alt="Flag"
                      class="h-4 w-4 mr-2 rounded-full"
                    />
                    {{ country.name }}
                  </div>
                </div>
              </div>

              <!-- Contact Number -->
              <div class="flex items-center text-gray-600 dark:text-gray-300">
                <svg
                  class="h-5 w-5 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h3m-3-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"
                  />
                </svg>
                <a href="tel:+1234567890" class="hover:text-blue-600 dark:hover:text-blue-400">
                  +123 456 7890
                </a>
              </div>
            </div>

            <!-- Authentication Links -->
            <div class="flex items-center space-x-4">
              <template v-if="!$page.props.auth.user">
                <Link
                  href="/login"
                  class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition"
                >
                  Sign In
                </Link>
                <Link
                  href="/register"
                  class="btn btn-primary"
                >
                  Sign Up
                </Link>
              </template>
              <template v-else>
                <div class="relative">
                  <button
                    @click="toggleProfileDropdown"
                    class="flex items-center text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100"
                  >
                    <img
                      :src="$page.props.auth.user.avatar || '/default-avatar.png'"
                      alt="Profile"
                      class="h-8 w-8 rounded-full mr-2"
                    />
                    <span>{{ $page.props.auth.user.name }}</span>
                    <svg
                      class="ml-1 h-4 w-4"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>
                  <div
                    v-if="profileDropdownOpen"
                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-20"
                  >
                    <Link
                      href="/profile"
                      class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                      My Profile
                    </Link>
                    <Link
                      href="/my-bookings"
                      class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                      My Bookings
                    </Link>
                    <Link
                      href="/logout"
                      method="post"
                      class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-red-600"
                    >
                      Logout
                    </Link>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>
      </nav>

      <!-- Main Content -->
      <main class="flex-grow dark:bg-gray-950">
        <slot />
      </main>

      <!-- Footer -->
      <footer class="bg-white dark:bg-gray-900 shadow-md">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between items-center">
            <p class="text-gray-500 dark:text-gray-400">
              © {{ new Date().getFullYear() }} Rent & Rooms. All rights reserved.
            </p>
            <div class="flex space-x-4">
              <Link href="/about" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">About</Link>
              <Link href="/terms" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">Terms</Link>
              <Link href="/privacy" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">Privacy</Link>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </template>

  <script setup>
  import { ref } from "vue";
  import { Link } from "@inertiajs/vue3";

  const countries = [
    { code: "us", name: "United States", flag: "/flags/us.svg" },
    { code: "uk", name: "United Kingdom", flag: "/flags/uk.svg" },
  ];

  const countryDropdownOpen = ref(false);
  const profileDropdownOpen = ref(false);
  const selectedCountry = ref(countries[0]);
  const theme = ref("dark"); // Use "light" for the default light theme

  const toggleCountryDropdown = () => (countryDropdownOpen.value = !countryDropdownOpen.value);
  const toggleProfileDropdown = () => (profileDropdownOpen.value = !profileDropdownOpen.value);

  const selectCountry = (country) => {
    selectedCountry.value = country;
    countryDropdownOpen.value = false;
  };

  const toggleTheme = () => {
    theme.value = theme.value === "dark" ? "light" : "dark";
  };
  </script>
