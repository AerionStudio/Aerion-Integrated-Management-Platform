<template>
  <div class="container">
    <div class="card-container"><br>
      <el-card :class="cardClass" v-if="!isonclikatc && !isonclikpilot && !isonclikva"><br>
        <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
          <el-icon class="icon-1">
            <MapLocation/>
          </el-icon>
          在线地图
        </el-header>
        <br>
        <el-row :gutter="20">
          <el-col :span="6">
            <div class="grid-content ep-bg-purple"/>
            <el-button :type="weather" size="large" @click="loadweather">
              天气雷达
            </el-button>
          </el-col>
          <el-col :span="6">
            <div class="grid-content ep-bg-purple"/>
            <el-button :type="displaypilot_type" size="large" @click="displaypilot_change">
              在线机组
            </el-button>
          </el-col>
          <el-col :span="6">
            <div class="grid-content ep-bg-purple"/>
            <el-button :type="displayva_type" size="large" @click=" displayva_change">
              VA在线机组
            </el-button>
          </el-col>
          <el-col :span="6">
            <div class="grid-content ep-bg-purple"/>
            <el-button :type="displayatc_type" size="large" @click="displayatc_change">
              在线管制
            </el-button>
          </el-col>
        </el-row>

      </el-card>
      <br>
      <el-card :class="cardClass" v-if="isonclikatc">
        <el-button type="primary" @click="closemodel">关闭</el-button>
        <h1 style="font-size: 40px;color: #409EFF"> {{ onclickatcdata.callsign }} </h1>
        <h1 style="font-size: 20px ;color: #f9f9f9">{{ onclickatcdata.fq }}</h1>
        <h1 style="font-size: 20px ;color: #f9f9f9">{{ onclickatcdata.cid }}</h1>
      </el-card>
      <br>
      <el-card :class="cardClass" v-if="isonclikpilot">
        <el-button type="primary" @click="closemodel">关闭</el-button>
        <h1 style="font-size: 40px;color: #409EFF">{{ onclickpilotdata.callsign }} <p
            style="font-size: 20px ;color: #f9f9f9">
          {{ onclickpilotdata.flight_plan.departure }} ✈ {{ onclickpilotdata.flight_plan.arrival }}</p></h1>

        <el-card>
          <el-row :gutter="16">
            <el-col :span="8">
              <div class="statistic-card">
                <el-statistic :value="onclickpilotdata.groundspeed">
                  <template #title>
                    <div style="display: inline-flex; align-items: center">
                      地速
                    </div>
                  </template>
                </el-statistic>
              </div>
            </el-col>
            <el-col :span="8">
              <div class="statistic-card">
                <el-statistic :value="onclickpilotdata.altitude">
                  <template #title>
                    <div style="display: inline-flex; align-items: center">
                      高度
                    </div>
                  </template>
                </el-statistic>
              </div>
            </el-col>
            <el-col :span="8">
              <div class="statistic-card">
                <el-statistic :value="onclickpilotdata.heading">
                  <template #title>
                    <div style="display: inline-flex; align-items: center">
                      航向
                    </div>
                  </template>
                </el-statistic>
              </div>
            </el-col>

          </el-row>
        </el-card>
        <br>

        <el-card>
          <div class="demo-collapse">
            <el-collapse>
              <el-collapse-item title="详细信息" name="1">
                <h1>机长: {{ onclickpilotdata.cid }}</h1>
                <h1>在线时长: {{ onclickpilotdatamore.onlinetime }}</h1>
                <h1>机型: {{ onclickpilotdata.flight_plan.aircraft }}</h1>
                <h1>航高: {{ onclickpilotdata.flight_plan.altitude }}</h1>
                <h1>应答机: {{ onclickpilotdata.transponder }}</h1>
                <h1>航路: {{ onclickpilotdata.flight_plan.route }}</h1>
              </el-collapse-item>
            </el-collapse>
          </div>
        </el-card>


      </el-card>
      <br>
      <el-card :class="cardClass" v-if="isonclikva">
        <el-button type="primary" @click="closemodel">关闭</el-button>
        <h1 style="font-size: 40px;color: #409EFF">{{ onclickvaData.all.booking.callsign }} <p
            style="font-size: 20px ;color: #f9f9f9">
          {{ onclickvaData.dep.icao }} ✈ {{ onclickvaData.app.icao }}</p></h1>

        <el-card>
          <el-row :gutter="16">
            <el-col :span="8">
              <div class="statistic-card">
                <el-statistic :value="onclickvaData.all.progress.groundSpeed">
                  <template #title>
                    <div style="display: inline-flex; align-items: center">
                      地速
                    </div>
                  </template>
                </el-statistic>
              </div>
            </el-col>
            <el-col :span="8">
              <div class="statistic-card">
                <el-statistic :value="onclickvaData.all.progress.altitude">
                  <template #title>
                    <div style="display: inline-flex; align-items: center">
                      高度
                    </div>
                  </template>
                </el-statistic>
              </div>
            </el-col>
            <el-col :span="8">
              <div class="statistic-card">
                <el-statistic :value="onclickvaData.all.progress.magneticHeading">
                  <template #title>
                    <div style="display: inline-flex; align-items: center">
                      航向
                    </div>
                  </template>
                </el-statistic>
              </div>
            </el-col>

          </el-row>
        </el-card>
        <br>

        <el-card>
          <div class="demo-collapse">
            <el-collapse>
              <el-collapse-item title="详细信息" name="1">
                <h1>机长: {{ onclickvaData.pilot }}</h1>
                <h1>剩余时长: {{ onclickvaData.all.progress.timeRemaining }}</h1>
                <h1>乘客人数: {{ onclickvaData.all.booking.passengers }}</h1>
                <h1>货物重量: {{ onclickpilotdata.cargo }}</h1>
                <h1>航路: {{ onclickvaData.all.route.userRoute }}</h1>
              </el-collapse-item>
            </el-collapse>
          </div>
        </el-card>


      </el-card>
      <br>
      <el-card :class="cardClass" v-if="displaypilot">
        <el-table :data="pilot" border style="width: 100%" @row-click="handleRowClick">
          <el-table-column prop="callsign" label="航班号"/>
          <el-table-column prop="cid" label="机长"/>
          <el-table-column prop="flight_plan.departure" label="起飞机场"/>
          <el-table-column prop="flight_plan.arrival" label="落地机场"/>
          <el-table-column prop="flight_plan.aircraft" label="机型"/>
        </el-table>
      </el-card>
      <br>
      <el-card :class="cardClass" v-if="displayva">
        <el-table :data="vadata" border style="width: 100%" @row-click="handleRowClickVa">
          <el-table-column prop="id" label="ID"/>
          <el-table-column prop="callsign" label="航班号"/>
          <el-table-column prop="pilot" label="机长"/>
          <el-table-column prop="dep.icao" label="起飞机场"/>
          <el-table-column prop="app.icao" label="落地机场"/>
          <el-table-column prop="aircraft" label="机型"/>
          <el-table-column prop="state" label="状态">
            <template v-slot="{ row }">
              <el-tag :type="row.sta.tag" size="small">
                {{ row.sta.text }}
              </el-tag>
            </template>
          </el-table-column>
        </el-table>

      </el-card>
      <br>
      <el-card :class="cardClass" v-if="displayatc">
        <el-table :data="atc" border style="width: 100%" @row-click="handleRowAtcClick">
          <el-table-column prop="callsign" label="席位"/>
          <el-table-column prop="frequency" label="频率"/>
          <el-table-column prop="cid" label="管制人员"/>
        </el-table>
      </el-card>
    </div>
    <div ref="mapContainer" class="map-container"></div>

  </div>
</template>

<script>
import mapboxgl from 'mapbox-gl';
import {watch} from "vue";
import {isDark} from '../composables/dark';
import {MapLocation, WindPower} from "@element-plus/icons-vue";
import * as turf from '@turf/turf'; // Import Turf.js

export default {
  name: 'MapComponent',
  components: {WindPower, MapLocation},
  data() {
    return {
      map: null,
      markers: [],
      circleLayers: {},
      cardClass: null,
      pilot: [],
      atc: [],
      isonclikpilot: false,
      onclickpilotdata: [],
      isonclikatc: false,
      onclickatcdata: [],
      onclickpilotdatamore: [],
      intervalId: null,
      isRouteDisplayed: false,
      cid: null,
      time: null,
      weather: null,
      loadweathersta: 0,
      va: [],
      vadata: [],
      displaypilot: false,
      displayatc: false,
      displayva: false,
      displaypilot_type: null,
      displayatc_type: null,
      displayva_type: null,
      onclickvaData: [],
      isonclikva: false,

    };
  },
  mounted() {
    mapboxgl.accessToken = 'pk.eyJ1IjoiZ3VvYXI3c2J2ZCIsImEiOiJjbG9wd2s5dncwMThxMnFtazNncGxoZ3VyIn0.kxcLHaYXSoM8GfPpurqU7w'; // Replace with your Mapbox access token

    this.map = new mapboxgl.Map({
      container: this.$refs.mapContainer,
      style: 'mapbox://styles/guoar7sbvd/clwxpl9tc012w01rb4y28frgj',
      center: [116.397428, 39.90923],
      zoom: 3,
    });

    this.fetchAndUpdateData();
    this.upvadata(); // Corrected call
    this.getvadata();
    setInterval(this.getvadata, 5000);
    setInterval(this.upvadata, 5000); // Corrected call
    setInterval(this.fetchAndUpdateData, 5000);
  },
  created() {
    watch(
        isDark,
        (newValue) => {
          this.cardClass = newValue ? 'custom-card-dark' : 'custom-card';
        },
        {immediate: true}
    );
  },
  methods: {
    displaypilot_change() {
      this.displaypilot = !this.displaypilot;
      if (this.displaypilot) {
        this.displaypilot_type = 'primary';
      } else {
        this.displaypilot_type = null;
      }
    },
    displayatc_change() {
      this.displayatc = !this.displayatc;
      if (this.displayatc) {
        this.displayatc_type = 'primary';
      } else {
        this.displayatc_type = null;
      }
    },
    displayva_change() {
      this.displayva = !this.displayva;
      if (this.displayva) {
        this.displayva_type = 'primary';
      } else {
        this.displayva_type = null;
      }
    },
    async fetchData(url) {
      try {
        const response = await fetch(url);
        const data = await response.json();
        return data;
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    },
    async fetchAndUpdateData() {
      const data = await this.fetchData('https://server.skydreamclub.cn/whazzup.php');

      // Clear existing markers and circles
      this.markers.forEach(marker => marker.remove());
      this.markers = [];

      // Remove existing circle layers and sources
      Object.keys(this.circleLayers).forEach(layerId => {
        if (this.map.getLayer(layerId)) {
          this.map.removeLayer(layerId);
        }
        if (this.map.getSource(layerId)) {
          this.map.removeSource(layerId);
        }
      });
      this.circleLayers = {};
      this.pilot = data.pilot;
      this.atc = data.controllers;
      if (data.controllers && data.controllers.length > 0) {
        data.controllers.forEach(atc => {
          const atcMarker = new mapboxgl.Marker({
            element: this.createMarkerElementAtc('fas fa-broadcast-tower', '#FF0000', atc.heading, atc.latitude, atc.longitude, atc.cid, atc.frequency, atc.callsign, atc.rating),
            anchor: 'bottom',
          })
              .setLngLat([atc.longitude, atc.latitude])
              .addTo(this.map)
              .on('click', () => {
                this.openmodel(atc, atc);
                this.map.flyTo({
                  center: [atc.longitude, atc.latitude],
                  essential: true,
                });
              });

          this.markers.push(atcMarker);

          // Add a circle layer
          const sourceId = `circle-source-${atc.cid}`;
          const layerId = `circle-layer-${atc.cid}`;

          this.circleLayers[layerId] = true;

          fetch(`https://server.skydreamclub.cn/GetAtcCoord.php?atc_id=${atc.callsign}`, {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json'
            },
          })
              .then(response => response.json())
              .then(data => {
                if (data.status == '200') {
                  const coordinates = data.data[0];

                  // Check if the source already exists and remove it if necessary
                  if (this.map.getSource(sourceId)) {
                    this.map.removeSource(sourceId);
                  }

                  // Add the source
                  this.map.addSource(sourceId, {
                    type: 'geojson',
                    data: {
                      type: 'Feature',
                      geometry: {
                        type: 'Polygon',
                        coordinates: [coordinates],
                      },
                    },
                  });

                  // Add the fill layer
                  this.map.addLayer({
                    id: layerId,
                    type: 'fill',
                    source: sourceId,
                    layout: {},
                    paint: {
                      'fill-color': '#088',
                      'fill-opacity': 0.4,
                    },
                  });

                  const center = turf.centerOfMass({
                    type: 'Feature',
                    properties: {},
                    geometry: {
                      type: 'Polygon',
                      coordinates: [coordinates],
                    },
                  });

                  // Add the text layer
                  this.map.addLayer({
                    id: 'textLayer' + layerId,
                    type: 'symbol',
                    source: {
                      type: 'geojson',
                      data: {
                        type: 'FeatureCollection',
                        features: [{
                          type: 'Feature',
                          geometry: {
                            type: 'Point',
                            coordinates: center.geometry.coordinates,
                          },
                          properties: {
                            title: atc.callsign,
                          },
                        }],
                      },
                    },
                    layout: {
                      'text-field': ['get', 'title'],
                      'text-size': 14,
                      'text-offset': [0, 0.5],
                    },
                    paint: {
                      'text-color': '#fff',
                      'text-halo-color': '#000',
                      'text-halo-width': 2,
                    },
                  });

                }
              })
        });
      }

      if (data.pilot && data.pilot.length > 0) {
        data.pilot.forEach(pilot => {
          const aircraftMarker = new mapboxgl.Marker({
            element: this.createMarkerElementPilot('fas fa-plane', '#e8b004', pilot.heading, pilot.latitude, pilot.longitude, pilot.callsign, pilot.logon_time, pilot.cid),
            anchor: 'bottom',
          })
              .setLngLat([pilot.longitude, pilot.latitude])
              .addTo(this.map)
              .on('click', () => {
                this.map.flyTo({
                  center: [pilot.longitude, pilot.latitude],
                  essential: true,
                });
              });

          this.markers.push(aircraftMarker);
        });
      }
    },
    createMarkerElementPilot(iconClass, iconColor, rotation, lat, lon, callsign, time, cid) {
      const markerElement = document.createElement('div');
      markerElement.className = 'custom-marker';

      const iconElement = document.createElement('i');
      iconElement.className = iconClass;
      iconElement.style = `font-size: 20px; color: ${iconColor}; transform: rotate(${rotation - 90}deg);`;

      const callsignElement = document.createElement('div');
      callsignElement.className = 'callsign';
      callsignElement.innerText = callsign;

      markerElement.appendChild(iconElement);
      markerElement.appendChild(callsignElement);

      markerElement.addEventListener('click', () => {
        this.openmodel(cid, lat, lon, time);
      });

      return markerElement;
    },
    createMarkerElementAtc(iconClass, iconColor, rotation, lat, lon, cid, fq, callsign, rating) {
      const markerElement = document.createElement('div');
      markerElement.className = 'custom-marker';
      markerElement.innerHTML = `<i class="${iconClass}" style="font-size: 20px; color: ${iconColor}; transform: rotate(${rotation - 90}deg);"></i>`;
      markerElement.addEventListener('click', () => {
        this.openmodelatc(cid, fq, callsign, lon, lat, rating);
        this.map.flyTo({
          center: [lon, lat],
          essential: true,
        });
      });
      return markerElement;
    },
    openmodel(cid, lat, lon, time) {
      this.closemodel();
      this.isonclikpilot = false;
      this.onclickpilotdata = [];
      this.onclickpilotdatamore = [];
      for (let i = 0; i < this.pilot.length; i++) {
        if (this.pilot[i].cid === cid) {
          this.onclickpilotdata = this.pilot[i];
        }
      }

      const api = "https://server.skydreamclub.cn/GetOnlineFlightDataByCid.php";
      const requestData = {
        token: 'ab321818',
        cid: cid,
        time: time
      };

      fetch(api, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(requestData)
      })
          .then(response => response.json())
          .then(data => {



            this.isonclikpilot = true;
            this.onclickpilotdatamore = data.data;
            // Assuming this.map is accessible
            this.map.flyTo({
              center: [lon, lat],
              zoom: 8,
              essential: true,
            });
            var longitudelist = data.data.lon;
            var latitudelist = data.data.lat;

            var coordinates = [];
            for (var i = 0; i < longitudelist.length; i++) {
              var longitude = parseFloat(longitudelist[i]);
              var latitude = parseFloat(latitudelist[i]);

              if (!isNaN(longitude) && !isNaN(latitude)) {
                coordinates.push([longitude, latitude]);
              } else {
                console.error('Invalid coordinates at index ' + i + ': ' + longitudelist[i] + ', ' + latitudelist[i]);
              }
            }


            // Add the new 'line' source and layer
            this.map.addSource('line', {
              'type': 'geojson',
              'data': {
                'type': 'Feature',
                'properties': {},
                'geometry': {
                  'type': 'LineString',
                  'coordinates': coordinates
                }
              }
            });

            this.map.addLayer({
              'id': 'line',
              'type': 'line',
              'source': 'line',
              'layout': {
                'line-join': 'round',
                'line-cap': 'round'
              },
              'paint': {
                'line-color': '#00cfff',
                'line-width': 5
              }
            });
          })
          .catch(error => {
            console.error('Error fetching data:', error);
          });
    },

    closemodel() {
      this.isonclikva = false;
      this.isonclikpilot = false;
      this.isonclikatc = false;
      this.onclickatcdata = [];
      this.onclickpilotdata = [];
      this.onclickvadata = [];
      this.onclickpilotdatamore = [];
      if (this.map.getSource('line')) {
        this.map.removeLayer('line');
        this.map.removeSource('line');
      }
      if (this.map.getSource('line-va-source')) {
        this.map.removeLayer('line-va');
        this.map.removeSource('line-va-source');
      }

    },
    openmodelatc(cid, fq, callsign, lon, lat, rating) {
      this.closemodel();
      const data = {
        cid: cid,
        fq: fq,
        callsign: callsign
      };
      this.onclickatcdata = data;
      this.isonclikatc = true;
      this.map.flyTo({
        center: [lon, lat],
        essential: true,
      });
    },
    openmodelva(booking) {
      this.closemodel();
      this.isonclikva = true;
      const lats = [];
      const lons = [];
      for (let i = 0; i < this.vadata.length; i++) {
        if (booking === this.vadata[i].id) {
          this.onclickvaData = this.vadata[i];
        }
      }
      this.map.flyTo({
        center: [this.onclickvaData.all.progress.location.lon, this.onclickvaData.all.progress.location.lat],
        zoom: 6,
        essential: true,
      });
      Object.values(this.onclickvaData.all.progress.posreps).forEach(posrep => {
        lats.push(parseFloat(posrep.lat));
        lons.push(parseFloat(posrep.lon));
      });
      var sampleRate = 20;
      var sampledCoordinates = [];

      for (var i = 0; i < lons.length; i += sampleRate) {
        var longitude = parseFloat(lons[i]);
        var latitude = parseFloat(lats[i]);
        if (!isNaN(longitude) && !isNaN(latitude)) {
          sampledCoordinates.push([longitude, latitude]);
        } else {
          console.error('Invalid coordinates at index ' + i + ': ' + lons[i] + ', ' + lats[i]);
        }
      }


      // Add the new 'line' source and layer
      this.map.addSource('line-va-source', {
        'type': 'geojson',
        'data': {
          'type': 'Feature',
          'properties': {},
          'geometry': {
            'type': 'LineString',
            'coordinates': sampledCoordinates
          }
        }
      });

      this.map.addLayer({
        'id': 'line-va',
        'type': 'line',
        'source': 'line-va-source',
        'layout': {
          'line-join': 'round',
          'line-cap': 'round'
        },
        'paint': {
          'line-color': '#00cfff',
          'line-width': 3
        }
      });

    },
    loadweather() {
      this.weather = 'success'
      if (this.map) {
        fetch("https://api.rainviewer.com/public/weather-maps.json")
            .then(res => res.json())
            .then(apiData => {
              apiData.radar.past.forEach(frame => {
                this.map.addLayer({
                  id: `rainviewer_${frame.path}`,
                  type: "raster",
                  source: {
                    type: "raster",
                    tiles: [
                      apiData.host + frame.path + '/256/{z}/{x}/{y}/2/1_1.png'
                    ],
                    tileSize: 256
                  },
                  layout: {visibility: "none"},
                  minzoom: 0,
                  maxzoom: 12
                });
              });

              let i = 0;
              const interval = setInterval(() => {
                if (i > apiData.radar.past.length - 1) {
                  clearInterval(interval);
                  return;
                } else {
                  apiData.radar.past.forEach((frame, index) => {
                    this.map.setLayoutProperty(
                        `rainviewer_${frame.path}`,
                        "visibility",
                        index === i || index === i - 1 ? "visible" : "none"
                    );
                  });
                  if (i - 1 >= 0) {
                    const frame = apiData.radar.past[i - 1];
                    let opacity = 1;
                    setTimeout(() => {
                      const i2 = setInterval(() => {
                        if (opacity <= 0) {
                          return clearInterval(i2);
                        }
                        this.map.setPaintProperty(
                            `rainviewer_${frame.path}`,
                            "raster-opacity",
                            opacity
                        );
                        opacity -= 0.1;
                      }, 50);
                    }, 400);
                  }
                  i += 1;
                }
              }, 2000);
            })
            .catch(console.error);
      }
    },
    handleRowClick(row) {
      this.openmodel(row.cid, row.latitude, row.longitude, row.logon_time);
    },
    handleRowClickVa(row) {
      this.openmodelva(row.id);
    },
    handleRowAtcClick(row) {
      this.openmodelatc(row.cid, row.frequency, row.callsign, row.longitude, row.latitude, row.rating);
    },
    createMarkerElementPilotVa(iconClass, iconColor, rotation, lat, lon, time, cid, booking, callsign) {

      const markerElement = document.createElement('div');
      markerElement.className = 'custom-marker';

      const iconElement = document.createElement('i');
      iconElement.className = iconClass;
      iconElement.style = `font-size: 20px; color: ${iconColor}; transform: rotate(${rotation - 90}deg);`;

      const callsignElement = document.createElement('div');
      callsignElement.className = 'callsign';
      callsignElement.innerText = callsign;

      markerElement.appendChild(iconElement);
      markerElement.appendChild(callsignElement);

      markerElement.addEventListener('click', () => {
        this.openmodelva(booking);
      });

      return markerElement;
    },
    updateVaMarkerPositions(data) {
      if (data.flights) {
        Object.values(data.flights).forEach(flight => {
          var aircraftMarkerData = this.va.find(markerData => markerData.cid === flight.pilot.username);
          if (aircraftMarkerData) {
            aircraftMarkerData.marker.setLngLat([flight.progress.location.lon, flight.progress.location.lat]);
          } else {
            var aircraftMarker = new mapboxgl.Marker({
              element: this.createMarkerElementPilotVa('fas fa-plane', '#DC143C', flight.progress.magneticHeading, flight.progress.location.lat, flight.progress.location.lon, flight.progress.departureTime, flight.pilot.username, flight.bookingId, flight.booking.callsign),
              anchor: 'bottom',
            })
                .setLngLat([flight.progress.location.lon, flight.progress.location.lat])
                .addTo(this.map)
                .on('click', function () {
                  // Move the map center to the clicked aircraft marker
                  this.map.flyTo({
                    center: [flight.progress.location.lon, flight.progress.location.lat],
                    essential: true, // this animation is considered essential with respect to prefers-reduced-motion
                  });
                });
            this.va.push({cid: flight.pilot.username, marker: aircraftMarker});
          }
        });
      }
    },
    upvadata() {
      fetch('https://vamsys.io/statistics/v2/map/96b29035-8200-4eb4-be80-7fd9fff745be')
          .then(response => response.json()) // Parse the JSON response
          .then(data => {
            this.updateVaMarkerPositions(data); // Update marker positions with the fetched data
          })
          .catch(error => {
            console.error('Error fetching data:', error);
          });
    },
    getvadata() {
      const apiEndpoint = 'https://vamsys.io/statistics/v2/map/96b29035-8200-4eb4-be80-7fd9fff745be';
      fetch(apiEndpoint, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        },
      })
          .then(response => {
            return response.json();
          })
          .then(data => {
            let flightKeys = Object.keys(data.flights);
            const data_va = [];
            for (let i = 0; i < flightKeys.length; i++) {
              data_va[i] = {
                'id': data.flights[flightKeys[i]].bookingId,
                'callsign': data.flights[flightKeys[i]].booking.callsign,
                'pilot': data.flights[flightKeys[i]].pilot.username,
                'dep': {
                  'icao': data.flights[flightKeys[i]].departureAirport.icao,
                  'name': data.flights[flightKeys[i]].departureAirport.name
                },
                'app': {
                  'icao': data.flights[flightKeys[i]].arrivalAirport.icao,
                  'name': data.flights[flightKeys[i]].arrivalAirport.name
                },
                'aircraft': data.flights[flightKeys[i]].aircraft.code,
                'sta': {
                  'tag': this.getvatag(data.flights[flightKeys[i]].progress.currentPhase),
                  'text': this.getvatext(data.flights[flightKeys[i]].progress.currentPhase)
                },
                'all': data.flights[flightKeys[i]],
              };
            }
            this.vadata = data_va;
          })
          .catch(error => {
            // Handle errors
            console.error('There was a problem with the fetch operation:', error);
          });
    },
    getvatag(id) {
      switch (id) {
        case 'Preflight':
          return 'primary';

        case 'Taxiing to Runway':
          return 'info';

        case 'Climbing':
          return 'warning';

        case 'Cruising':
          return 'success';

        case 'Descending':
          return 'warning';

        case 'Pushing Back':
          return 'info';
        case 'Approaching':
          return 'primary';

        case 'Final Approach':
          return 'primary';

        case 'Taking Off':
          return 'info';

        case 'Landing':
          return 'info';

        case 'Arrived':
          return 'primary';

        case 'Taxiing to Gate':
          return 'info';

        default:
          return 'info';

      }
    },
    getvatext(id) {
      switch (id) {
        case 'Preflight':
          return '航前准备';
        case 'Taxiing to Runway':
          return '滑行至跑道';
        case 'Climbing':
          return '爬升';
        case 'Cruising':
          return '巡航';
        case 'Descending':
          return '下降';
        case 'Pushing Back':
          return '推出开车';
        case 'Approaching':
          return '进近';
        case 'Final Approach':
          return '最终进近';
        case 'Taking Off':
          return '起飞';
        case 'Landing':
          return '落地';
        case 'Arrived':
          return '到达';
        case 'Taxiing to Gate':
          return '滑行至登机口';
        default:
          return '未知状态,请联系管理员';
      }
    },


  },
  filters: {
    flightPlanCheck(value) {
      return value ? value : 'N/A';
    }
  }
};
</script>

<style>
.map-container {
  width: 120%;
  height: 100vh;
}

.custom-marker {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.custom-marker .callsign {
  font-size: 12px;
  color: #ffffff;
  background: rgba(0, 0, 0, 0.5);
  padding: 2px 4px;
  border-radius: 00px;
  margin-top: 2px;
}

.custom-marker i {
  font-size: 20px;
}

.container {
  position: relative;
  width: 120%;
  height: 100vh;
}

.map-container {
  width: 120%;
  height: 100%;
}

.el-fade-in-linear-enter-active, .el-fade-in-linear-leave-active {
  transition: opacity 0.5s linear;
}

.el-fade-in-linear-enter, .el-fade-in-linear-leave-to {
  opacity: 0;
}

.card-container {
  position: absolute;
  top: 10px;
  left: 30px;
  z-index: 1; /* Ensure cards are above the map */
  width: 650px; /* Adjust card container width as needed */
}

.title-container {
  position: absolute;
  z-index: 1; /* Ensure cards are above the map */
}

.custom-card:hover,
.custom-card-dark:hover {
  transform: translateY(-10px);
  transition: transform 0.5s ease;
}

.custom-card {
  background-color: rgba(255, 255, 255, 0.5); /* Semi-transparent background */
  backdrop-filter: blur(10px); /* Gaussian blur */
  -webkit-backdrop-filter: blur(10px); /* Safari support */
  border: 1px solid rgba(255, 255, 255, 0.5); /* Semi-transparent border */
}

.custom-card-dark {
  background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
  backdrop-filter: blur(10px); /* Gaussian blur */
  -webkit-backdrop-filter: blur(10px); /* Safari support */
  border: 1px solid rgba(255, 255, 255, 0.5); /* Semi-transparent border */
}

.icon-1 {
  position: relative;
}

.icon-1::after {
  content: "";
  position: absolute;
  bottom: -25px;
  left: 0;
  width: 350%;
  height: 6px;
  background-color: #66b1ff;
  border-radius: 10px;
}
</style>
