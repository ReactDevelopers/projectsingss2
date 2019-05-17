var CACHE_NAME = 'contract-lodge-cache-v1';
// Define url to get the cache url's list.
var cacheUrlsListUrl = '/api/cache-urls-list';

// Add install event
self.addEventListener('install', function(event) {
    //console.log("Service Worker Install");
	// Perform install steps
	event.waitUntil(
        fetch(cacheUrlsListUrl)
            .then(response => response.json())
            .then(urlsToCache => {
                caches.open(CACHE_NAME)
                    .then(function(cache) {
                        //console.log("Service Worker cache");
                        return cache.addAll(urlsToCache);
                    })
            })
	);
});

// Add fetch event
self.addEventListener('fetch', function(event) {
 	event.respondWith(
    	caches.match(event.request).then(function(res) {
            //console.log('[Service Worker] Fetching resource: ' + event.request.url);
            if (navigator.onLine === true) {
                //console.log("Online");
                return fetch(event.request).then(function(response) {
                    if (event.request.method !== "GET") {
                        return response;
                    }
                    return caches.open(CACHE_NAME).then(function(cache) {
                        //console.log('[Service Worker] Caching new resource: ' + event.request.url);
                        cache.put(event.request, response.clone());
                        return response;
                    });
                });
            } else {
                //console.log("Offline");
                return res || fetch(event.request).then(function(response) {
                    return caches.open(CACHE_NAME).then(function(cache) {
                        //console.log('[Service Worker] Caching new resource: '+event.request.url);
                        cache.put(event.request, response.clone());
                        return response;
                    });
                });
            }
    	})
  	);
});
