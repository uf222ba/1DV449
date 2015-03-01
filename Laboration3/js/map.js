/**
 * Student Id:  uf222ba
 * Name:        Ulrika Falk
 * Mail:        uf222ba@student.lnu.se
 * Date:        2015-02-16
 * Laboration:  Laboration 3, Webbteknik II 1DV449
 */

var Map = {

    events: [],
    allEvents: null,
    roadTrafficEvents: null,
    publicTranspEvents: null,
    plannedEvents: null,
    otherEvents: null,
    numOfAllEv: null,
    numOfRoadTrafficEv: null,
    numOfPublicTranspEv: null,
    numOfPlannedEv: null,
    numOfOtherEv: null,
    all: 0,
    vt: 0,
    kt: 0,
    ps: 0,
    ov: 0,
    markers: [],
    categories:[],
    showMarkers: [],
    point: [],
    map: null,
    openInfoWindow: null,

    init:function(e) {
        //Map.initMap();
        Map.allEvents = document.getElementById("allEvents");
        Map.roadTrafficEvents = document.getElementById("roadTrafficEvents");
        Map.publicTranspEvents = document.getElementById("publicTranspEvents");
        Map.plannedEvents = document.getElementById("plannedEvents");
        Map.otherEvents = document.getElementById("otherEvents");

        Map.numOfAllEv = document.getElementById("numOfAllEv");
        Map.numOfRoadTrafficEv = document.getElementById("numOfRoadTrafficEv");
        Map.numOfPublicTranspEv = document.getElementById("numOfPublicTranspEv");
        Map.numOfPlannedEv = document.getElementById("numOfPlannedEv");
        Map.numOfOtherEv = document.getElementById("numOfOtherEv");

        document.getElementById("linkAllEv").onclick = function(e) {Map.renderCategory(4); return false;}
        document.getElementById("linkRoadTrafficEv").onclick = function(e) {Map.renderCategory(0); return false;}
        document.getElementById("linkPublicTrafficEv").onclick = function(e) {Map.renderCategory(1); return false;}
        document.getElementById("linkPlannedEv").onclick = function(e) {Map.renderCategory(2); return false;}
        document.getElementById("linkOtherEv").onclick = function(e) {Map.renderCategory(3); return false;}

        Map.initMap();
        Map.getEvents();
    },

    initMap:function() {

        Map.map = new google.maps.Map(document.getElementById('map'), {
            zoom: 5,
            center: new google.maps.LatLng(62.796353, 15.470043),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl: false,
            streetViewControl: false,
            panControl: false,
            zoomControlOptions: {
                position: google.maps.ControlPosition.LEFT_BOTTOM
            }
        });
    },

    getEvents:function() {
        $.ajax({
            type: "GET",
            url: "get.php"
        }).done(function(data) {
            data = JSON.parse(data);

            for(var i in data.messages) {

                var obj = data.messages[i]; // Gör varje element i arrayen av returnerat data till ett objekt.
                //Fixa tiden - gör en funktion av det eller lägg till det i prototypen
                var unixTime = obj.createddate.substring(6,19);
                var e = new Event(obj.id, obj.priority, obj.exactlocation, obj.title, obj.description, unixTime, obj.latitude, obj.longitude, obj.category, obj.subcategory); //new Message(text, new Date());
                var eventId = Map.events.push(e) - 1;
                //**Map.renderEvent(eventId, e.getCategory()); // Skriv ut meddelandet i webbläsarfönstret.
            }

            Map.events.reverse();   // Eftersom sort-parametern i API:et inte verkar fungera, så får jag vända på innehållet istället

            if(Map.events.length > 100) {   // Om svaret innehåller fler än 100 poster, så kastar jag de som överstiger 100.
                var deleted = Map.events.splice(100, Number.MAX_VALUE);
            }

            for(var cat = 0; cat < 4; cat++) {
                var arr = $.grep(Map.events, function(Event) {
                    return Event.getCategory() == cat;
                });
                Map.categories[cat] = arr;
            }

            Map.categories[4] = Map.events;

            // Sätter antalet händelser per kategori
            Map.numOfAllEv.innerHTML = Map.categories[4].length
            Map.numOfRoadTrafficEv.innerHTML = Map.categories[0].length;
            Map.numOfPublicTranspEv.innerHTML = Map.categories[1].length;
            Map.numOfPlannedEv.innerHTML = Map.categories[2].length;
            Map.numOfOtherEv.innerHTML = Map.categories[3].length;

            // Anropa renderEvents
            for(var i = 0; i <Map.categories.length; i++) {
                if(Map.categories[i].length > 0) {
                    Map.renderEvents(i);
                }
            }

        }).fail(function(e) {
            console.log(e);
        });
    },
    // Vet att det är störande upprepning av kod nedan, som borde läggas i en funktion, men det vill sig inte...
    renderEvents: function(category) {
        switch (category) {
            case 0:
                for (var eachEvent in Map.categories[0]) {
                    var link = document.createElement("a");
                    link.id = Map.categories[0][eachEvent].getId();
                    link.onclick = (function(i) {
                        return function() {
                            Map.showInfoWindow(i);
                        }
                    })(eachEvent);
                    link.href = "#";
                    link.className = "list-group-item small";
                    link.innerHTML = Map.categories[0][eachEvent].getDateText() + " " + Map.categories[0][eachEvent].getTitle();
                    Map.roadTrafficEvents.appendChild(link);
                }
                break;
            case 1:
                for (var eachEvent in Map.categories[1]) {
                    var link = document.createElement("a");
                    link.id = Map.categories[1][eachEvent].getId();
                    link.onclick = (function(i) {
                        return function() {
                            Map.showInfoWindow(i);
                        }
                    })(eachEvent);
                    link.href = "#";
                    link.className = "list-group-item small";
                    link.innerHTML = Map.categories[1][eachEvent].getDateText() + " " + Map.categories[1][eachEvent].getTitle();
                    Map.publicTranspEvents.appendChild(link);
                }
                break;
            case 2:
                for (var eachEvent in Map.categories[2]) {
                    var link = document.createElement("a");
                    link.id = Map.categories[2][eachEvent].getId();
                    link.onclick = (function(i) {
                        return function() {
                            Map.showInfoWindow(i);
                        }
                    })(eachEvent);
                    link.href = "#";
                    link.className = "list-group-item small";
                    link.innerHTML = Map.categories[2][eachEvent].getDateText() + " " + Map.categories[2][eachEvent].getTitle();
                    Map.plannedEvents.appendChild(link);
                }
                break;
            case 3:
                for(var eachEvent in Map.categories[3]) {
                    var link = document.createElement("a");
                    link.id = Map.categories[3][eachEvent].getId();
                    link.onclick = (function(i) {
                        return function() {
                            Map.showInfoWindow(i);
                        }
                    })(eachEvent);
                    link.href = "#";
                    link.className = "list-group-item small";
                    link.innerHTML = Map.categories[3][eachEvent].getDateText() + " " + Map.categories[3][eachEvent].getTitle();
                    Map.otherEvents.appendChild(link);
                }
                //renderEvent(3);
                break;
            case 4:
                for(var eachEvent in Map.categories[4]) {
                    var link = document.createElement("a");
                    link.id = Map.categories[4][eachEvent].getId();
                    link.onclick = (function(i) {
                        return function() {
                            Map.showInfoWindow(i);
                        }
                    })(eachEvent);
                    link.href = "#";
                    link.className = "list-group-item small";
                    link.innerHTML = Map.categories[4][eachEvent].getDateText() + " " + Map.categories[4][eachEvent].getTitle();
                    Map.allEvents.appendChild(link);
                }
                break;
            default:
                console.log("Error: unknown category");
                break;
        }
    },

    renderCategory: function(category) {
        Map.deleteMarkers();

        for(var eachEvent in Map.categories[category]) {
            Map.showMarkers.push(Map.categories[category][eachEvent].getMarkerData());
        }

        Map.renderMarkers(Map.showMarkers);
    },

    deleteMarkers:function() {
        Map.showMarkers = [];
        //Map.markers = [];
          // Sets the map on all markers in the array.
        for (var i = 0; i < Map.markers.length; i++) {
            Map.markers[i].setMap(null);
        }
        Map.marker = [];
        Map.markers = [];
    },

    renderMarkers:function(point) { // OBS! Koden kommer härifrån: http://chrisltd.com/blog/2013/08/google-map-random-color-pins/

        // Setup the different icons and shadows
        var iconURLPrefix = 'http://maps.google.com/mapfiles/ms/icons/';

        var icons = [
            iconURLPrefix + 'red-dot.png',      // Mycket allvarlig händelse
            iconURLPrefix + 'orange-dot.png',   // Stor händelse
            iconURLPrefix + 'yellow-dot.png',   // Störning
            iconURLPrefix + 'green-dot.png',    // Information
            iconURLPrefix + 'pink-dot.png'      // Mindre störning
        ]
        var iconsLength = icons.length;

        var infoWindow = new google.maps.InfoWindow({
            maxWidth: 300 //160
        });

        var iconCounter = 0;

        // Add the markers and infowindows to the map
        for (var i = 0; i < point.length; i++) {
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(point[i][1], point[i][2]),
                map: Map.map,
                //icon: icons[iconCounter]
                icon: icons[(point[i][4])-1]
            });

            Map.markers.push(marker);

            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    if(Map.openInfoWindow != null) {
                        infoWindow.close(Map.openInfoWindow);
                        Map.openInfoWindow = null;
                    }
                    //infoWindow.setContent(point[i][0]);
                    infoWindow.setContent(point[i][0]);
                    infoWindow.open(Map.map, marker);
                    Map.openInfoWindow = marker;
                }
            })(marker, i));

            iconCounter++;
            // We only have a limited number of possible icon colors, so we may have to restart the counter
            if(iconCounter >= iconsLength) {
                iconCounter = 0;
            }
        }

        function autoCenter() {
            //  Create a new viewpoint bound
            var bounds = new google.maps.LatLngBounds();
            //  Go through each...
            for (var i = 0; i < Map.markers.length; i++) {
                bounds.extend(Map.markers[i].position);
            }
            //  Fit these bounds to the map
            Map.map.fitBounds(bounds);
        }
        autoCenter();
    },

    showInfoWindow: function(key) {
        google.maps.event.trigger(Map.markers[key],'click');
    }
}

window.onload = Map.init;

function Event(id, priority, exactlocation, title, description, date, latitude, longitude, category, subcategory){
    this.getId = function() {
        return id;
    }
    this.setId = function(_id) {
        id = id;
    }
    this.getPriority = function() {
        return priority;
    }
    this.setPriority = function(_priority) {
        priority = priority;
    }
    this.getExactLocation = function() {
        return exactlocation;
    }
    this.setExactLocation = function(_exactlocation) {
        exactlocation = exactlocation;
    }
    this.getTitle = function() {
        return title;
    }
    this.setTitle = function(_title) {
        title = title;
    }
    this.getDescription = function() {
        return description;
    }
    this.setDescription = function(_description) {
        description = description;
    }
    this.getDate = function() {
        return date;
    }
    this.setDate = function(_date) {
        date = date;
    }
    this.getLatitude = function() {
        return latitude;
    }
    this.setLatitude = function(_latitude) {
        latitude = latitude;
    }
    this.getLongitude = function() {
        return longitude;
    }
    this.setLongitude = function(_longitude) {
        longitude = longitude;
    }
    this.getCategory = function() {
        return category;
    }
    this.setCategory = function(_category) {
        category = category;
    }
    this.getSubCategory = function() {
        return subcategory;
    }
    this.setSubCategory = function(_subcategory) {
        subcategory = subcategory;
    }
}

Event.prototype.getDateText = function() {
    var d = new Date();
    d.setTime(this.getDate());
    var n = d.toISOString();
    n = n.substring(0, 16);
    n = n.replace("T", " ");
    return n;
}

Event.prototype.getPriorityText = function() {
    var priorityTypeText;
    switch(this.getPriority()) {
        case 1:
            priorityTypeText = "Mycket allvarlig händelse";
            break;
        case 2:
            priorityTypeText = "Stor händelse";
            break;
        case 3:
            priorityTypeText = "Störning";
            break;
        case 4:
            priorityTypeText = "Information";
            break;
        case 5:
            priorityTypeText = "Mindre störning";
            break;
        default:
            priorityTypeText = "Unknown priority";
            break;
    }
    return priorityTypeText;
}


Event.prototype.getCategoryText = function() {
    var categoryTypeText;
    switch(this.getCategory()) {
        case 0:
            categoryTypeText = "Vägtrafik";
            break;
        case 1:
            categoryTypeText = "Kollektivtrafik";
            break;
        case 2:
            categoryTypeText = "Planerad störning";
            break;
        case 3:
            categoryTypeText = "Övrigt";
            break;
        default:
            categoryTypeText = "Unknown category";
            break;
    }
    return categoryTypeText;
}

Event.prototype.getMarkerData = function() {
    var markerArray = [];
    //markerArray[0] = "<h4>" + this.getTitle() + "</h4>";
    markerArray[0] = this.getEventContentHTML();
    markerArray[1] = this.getLatitude();
    markerArray[2] = this.getLongitude();
    markerArray[3] = this.getId();
    markerArray[4] = this.getPriority();
    return markerArray;
}

Event.prototype.getEventContentHTML = function() {
    var htmlStr = "";
    htmlStr = "<b>" + this.getTitle() + "</b><br />";
    htmlStr += "Tidpunkt: " + this.getDateText() +  "<br />";
    htmlStr += "Prioritet: " + this.getPriorityText() +  "<br />";
    if(this.getSubCategory().trim().length > 0) {
        htmlStr += "Kategori: " + this.getCategoryText() + ", " + this.getSubCategory() +  "<br />";
    } else {
        htmlStr += "Kategori: " + this.getCategoryText() + "<br />";
    }
    if(this.getExactLocation().trim().length > 0) {
        htmlStr += "Plats: " + this.getExactLocation() + "<br />";
    }
    if(this.getDescription().trim().length > 0) {
        htmlStr += "Beskrivning: " + this.getDescription() + "<br />";
    }

    return htmlStr;
}
