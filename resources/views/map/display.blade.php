<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>{{ $group->name }}s Map</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Load MapLibre GL JS and CSS from VersaTiles -->
  <script src="https://tiles.versatiles.org/assets/lib/maplibre-gl/maplibre-gl.js"></script>
  <link href="https://tiles.versatiles.org/assets/lib/maplibre-gl/maplibre-gl.css" rel="stylesheet" />
  <!-- Alpine.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <link rel="manifest" href="/manifest.json" />
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <link rel="apple-touch-icon" href="/favicon-192x192.png">
  <link rel="icon" sizes="192x192" href="/favicon-192x192.png">
  <style>
    /* Modern popup styles */
    .maplibregl-popup-content {
      background-color: white;
      color: #333;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
      padding: 15px;
      border-radius: 8px;
      font-family: 'Inter', sans-serif;
    }

    .maplibregl-popup-content h3 {
      margin-top: 0;
      margin-bottom: 8px;
      font-size: 1.2rem;
      font-weight: 600;
    }

    .maplibregl-popup-content p {
      margin: 0;
      font-size: 1rem;
      line-height: 1.4;
      margin-bottom: 8px; /* Add some margin below description */
    }
    .maplibregl-popup-content a.address {
      color: blue; /* Link color */
      text-decoration: underline;
    }
    .maplibregl-popup-content a.address:hover {
      color: darkblue;
    }
    .dark-mode .maplibregl-popup-content a.address {
      color: lightblue; /* Link color in dark mode */
    }
    .dark-mode .maplibregl-popup-content a.address:hover {
      color: white; /* Hover color in dark mode */
    }


    .maplibregl-popup-close-button {
      color: #777;
      font-size: 1.2rem;
      padding: 5px 8px;
      cursor: pointer;
      background: transparent;
      border: none;
      position: absolute;
      top: 0;
      right: 0;
      z-index: 2;
    }

    .maplibregl-popup-close-button:hover {
      color: #333;
      background-color: rgba(0,0,0,0.05);
    }

    /* Dark mode popup styles */
    .dark-mode .maplibregl-popup-content {
      background-color: #2D3748;
      color: #CBD5E0;
      box-shadow: 0 2px 8px rgba(255, 255, 255, 0.08);
    }

    .dark-mode .maplibregl-popup-close-button {
      color: #A0AEC0;
    }

    .dark-mode .maplibregl-popup-close-button:hover {
      color: #ffffff;
      background-color: rgba(255,255,255,0.1);
    }

    /* Custom map marker styles */
    .map-marker {
      width: 0px; /* Slightly larger markers */
      height: 0px;
      cursor: pointer;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 32px; /* Larger emoji size */
      color: white; /* Emoji color */
    }

    /* User location marker styles */
    .user-location-marker {
      width: 14px;
      height: 14px;
      border-radius: 50%;
      background-color: #3498db; /* Blue color */
      border: 2px solid white;
      animation: pulse 2s infinite; /* Pulsating animation */
    }

    @keyframes pulse {
      0% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.4); opacity: 0.5; }
      100% { transform: scale(1); opacity: 1; }
    }
  </style>
</head>
<body class="h-screen overflow-hidden font-sans antialiased" x-data="{ darkMode: true, sidebarOpen: false }" x-bind:class="{ 'dark-mode': darkMode }">
  <div class="flex h-full">
    <!-- Sidebar -->
    <div id="sidebar" class="w-64 bg-gray-100 text-gray-800 p-6 overflow-y-auto transition-transform duration-300 md:relative absolute z-30 md:translate-x-0 md:bg-white shadow-md dark:bg-gray-800 dark:text-white" :class="{ '-translate-x-full': !sidebarOpen }">
      <h2 class="text-2xl font-semibold mb-5 text-gray-900 dark:text-gray-100 mt-8">{{ $group->name }}</h2>
      <!-- Dark mode toggle -->
      <div class="mb-4">
        <label for="toggle-darkmode" class="inline-flex items-center cursor-pointer">
          <input type="checkbox" id="toggle-darkmode" class="sr-only peer" x-model="darkMode">
          <div class="relative w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
          <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Dark Mode</span>
        </label>
      </div>

      <hr class="border-gray-200 my-5 dark:border-gray-600">
      <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-gray-100">Cities</h3>
      <!-- City toggles: will be dynamically generated here -->
      <div id="city-list">
        </div>
    </div>
    <!-- Map container -->
    <div id="map" class="flex-1 relative"></div>
  </div>
  <!-- Hamburger menu button for mobile -->
  <button id="menu-button" class="fixed top-4 left-4 bg-white p-2 rounded shadow z-40 md:hidden hover:bg-gray-100" @click="sidebarOpen = !sidebarOpen"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
  </svg></button>

  <!-- Back button -->
  <a href="javascript:history.back()" class="fixed bottom-12 right-4 bg-white p-2 rounded shadow z-40 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white" title="Go back">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
      <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
    </svg>
  </a>

  <script>
    let currentStyle = 'https://tiles.versatiles.org/assets/styles/eclipse/style.json';
    const colorfulStyle = 'https://tiles.versatiles.org/assets/styles/colorful/style.json';

    const map = new maplibregl.Map({
      container: 'map',
      style: currentStyle,
      center: [{{ $center["lon"] }}, {{ $center["lat"] }}],
      zoom: 7
    });
    map.addControl(new maplibregl.NavigationControl());

    map.addControl(
        new maplibregl.GeolocateControl({
            positionOptions: {
                enableHighAccuracy: true
            },
            trackUserLocation: true
        })
    );

    // Close popup when clicking elsewhere on the map
    map.on('click', () => {
        if (currentOpenPopup) {
            currentOpenPopup.remove();
            currentOpenPopup = null;
        }
    });

    const cityData = {
    @foreach($markers as $marker)
      "{{ $marker->name }}": {
        coords: [{{ $marker->lon }}, {{ $marker->lat }}],
        description: "{{ $marker->description }}",
        markerIcon: '{{ $marker->emoji }}',
        address: "{{ $marker->address }}"
      },
    @endforeach
    };

    let currentOpenPopup = null;

    // Create markers and store them.
    const markers = {};
    for (const city in cityData) {
      const cityInfo = cityData[city];

      // Create a custom marker element
      const markerElement = document.createElement('div');
      markerElement.className = 'map-marker';
      markerElement.textContent = cityInfo.markerIcon;

      const popup = new maplibregl.Popup({
        offset: 25,
        closeButton: true,
        closeOnClick: false
      }).setHTML(`
        <h3>${city}</h3>
        <p>${cityInfo.description}</p>
        ${cityInfo.address ? `<p><a class="address" href="https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(cityInfo.address)}" target="_blank" rel="noopener">Get Directions</a></p>` : ''}
      `);

      popup.on('open', () => {
        if (currentOpenPopup && currentOpenPopup !== popup) {
          currentOpenPopup.remove();
        }
        currentOpenPopup = popup;

        setTimeout(() => {
          const popupContent = document.querySelector('.maplibregl-popup-content');
          if (popupContent) {
            popupContent.addEventListener('click', (e) => {
              e.stopPropagation(); // Prevent map click from closing popup
            });
          }

          const closeButton = document.querySelector('.maplibregl-popup-close-button');
          if (closeButton) {
            closeButton.addEventListener('click', (e) => {
              e.preventDefault();
              e.stopPropagation();
              popup.remove();
              currentOpenPopup = null;
            });
          }
        }, 100);
      });

      popup.on('close', () => {
        if (currentOpenPopup === popup) {
          currentOpenPopup = null;
        }
      });

      const marker = new maplibregl.Marker({ element: markerElement })
        .setLngLat(cityInfo.coords)
        .setPopup(popup);

      markerElement.addEventListener('click', () => {
        if (currentOpenPopup && currentOpenPopup !== popup) {
          currentOpenPopup.remove();
          currentOpenPopup = null;
        }
      });

      marker.addTo(map);
      markers[city] = marker;
    }

    function updateMarkers() {
      document.querySelectorAll('.city-checkbox').forEach(chk => {
        const city = chk.getAttribute('data-city');
        if (chk.checked) {
          if (!markers[city].getElement().parentNode) {
            markers[city].addTo(map);
          }
        } else {
          const markerPopup = markers[city].getPopup();
          if (markerPopup === currentOpenPopup) {
            currentOpenPopup.remove();
            currentOpenPopup = null;
          }
          markers[city].remove();
        }
      });
    }

    // Dynamically generate city list
    const cityListContainer = document.getElementById('city-list');
    for (const city in cityData) {
      const label = document.createElement('label');
      label.classList.add('block', 'mb-2', 'cursor-pointer', 'text-gray-700', 'dark:text-gray-300');
      const checkbox = document.createElement('input');
      checkbox.type = 'checkbox';
      checkbox.classList.add('city-checkbox', 'mr-2', 'rounded', 'border-gray-300', 'focus:ring-indigo-500', 'text-indigo-600', 'focus:border-indigo-500', 'dark:bg-gray-700', 'dark:border-gray-600', 'dark:checked:bg-indigo-600');
      checkbox.dataset.city = city;
      checkbox.checked = true;
      checkbox.addEventListener('change', updateMarkers);
      label.appendChild(checkbox);
      label.appendChild(document.createTextNode(city));
      cityListContainer.appendChild(label);
    }


    // Sidebar toggle for mobile
    const sidebar = document.getElementById('sidebar');
    const menuButton = document.getElementById('menu-button');

    // Ensure map resizes on window resize (no changes needed here)
    window.addEventListener('resize', () => {
      map.resize();
    });

    function updateMapStyle(isDarkMode) {
      // Close any open popup before changing style
      if (currentOpenPopup) {
        currentOpenPopup.remove();
        currentOpenPopup = null;
      }

      currentStyle = isDarkMode ? 'https://tiles.versatiles.org/assets/styles/eclipse/style.json' : colorfulStyle;
      map.setStyle(currentStyle);
      map.on('styledata', updateMarkers);
    }

    document.addEventListener('alpine:init', () => {
        Alpine.store('darkMode', { darkMode: true });
        Alpine.store('sidebarOpen', { sidebarOpen: false });

        Alpine.effect(() => {
            if (Alpine.store('darkMode')) {
                updateMapStyle(Alpine.store('darkMode').darkMode);
            }
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        if (Alpine.store('darkMode')) {
            updateMapStyle(Alpine.store('darkMode').darkMode);
        }
    });

     document.getElementById('toggle-darkmode').addEventListener('change', function() {
        if (Alpine.store('darkMode')) {
            Alpine.store('darkMode').darkMode = this.checked;
            updateMapStyle(this.checked);
        }
     });
  </script>
</body>
</html>
