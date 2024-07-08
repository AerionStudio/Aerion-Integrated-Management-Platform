<template>

  <transition name="el-fade-in-linear">
    <div class="container">
      <div class="card-container"><br>
        <el-alert style="text-align: center" v-show="load_main" title="正在加载数据请稍后" type="warning"/>
        <br v-show="load_main">
        <el-card :class="cardClass">
          <br>
          <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
            <el-icon class="icon-1">
              <Notebook/>
            </el-icon>
            活动报名
          </el-header>
        </el-card>
        <br>

        <el-card :class="cardClass">
          <h1 style="font-size: 25px;text-align: center" v-show="!load_main">起飞机场 / 落地机场</h1>
          <h1 style="font-size: 25px;text-align: center">
            <el-skeleton v-show="load_main" :rows="1"/>
            <span v-show="!load_main">{{ eventdata.dep_icao }} ✈ {{ eventdata.app_icao }}</span>
          </h1>
          <br>
          <h1 style="text-align: center" v-show="!load_main">活动时间</h1>
          <h2 style="text-align: center">
            <el-skeleton v-show="load_main" :rows="1"/>
            <span v-show="!load_main">{{ eventdata.time }}</span></h2>
        </el-card>
        <br>
        <el-card :class="cardClass" v-show="!load_main" style="text-align: center;cursor: pointer;" @click="gotodetail(eventdata.time)">
          <h1>点击查看活动</h1>
        </el-card>
        <br>
        <el-card :class="cardClass" @click="openpast()"  style="width: 120px;cursor: pointer;">
          <h3 style="text-align: center;font-size: 20px;" >点击查看既往活动</h3>
        </el-card>
      </div>

      <div ref="mapContainer" class="map-container"></div>
    </div>
  </transition>
</template>

<script>
import mapboxgl from 'mapbox-gl';
import {watch} from 'vue';
import {isDark} from '../composables/dark';
import {List, Notebook} from '@element-plus/icons-vue';
import axios from 'axios';
import depImage from '../assets/dep.png';
import appImage from '../assets/app.png';

export default {
  name: 'Map',
  components: {
    List,
    Notebook,
  },
  data() {
    return {
      map: null,
      center: [116.397428, 39.90923],
      cardClass: null,
      eventdata: [],
      load_main: true,
      waypoints: [],
      ishaveevent: 0,
    };
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
  mounted() {
    this.getevent();

    mapboxgl.accessToken = 'pk.eyJ1IjoiZ3VvYXI3c2J2ZCIsImEiOiJjbG9wd2s5dncwMThxMnFtazNncGxoZ3VyIn0.kxcLHaYXSoM8GfPpurqU7w';
    this.map = new mapboxgl.Map({
      container: this.$refs.mapContainer,
      style: 'mapbox://styles/guoar7sbvd/clwxpl9tc012w01rb4y28frgj',
      center: this.center, // Initial map center
      zoom: 3 // Initial map zoom level
    });

    // Ensure addWaypointsLine is called only after the map has fully loaded

  },
  methods: {
    async getevent() {
      try {
        this.loading = true;
        const response = await axios.post('https://server.skydreamclub.cn/GetEvents.php');
        const data = response.data;

        let time = [];
        for (let i = 0; i < data.length; i++) {
          if (data[i].state === '1') {
            this.ishaveevent = 1;
            this.eventdata = data[i];
            time = data[i].time;
          }
        }
        if (time.length > 0) {
          console.log(time);
          this.geteventmap(time);
        }

        if (!this.ishaveevent) {
          this.$router.push({name: 'noevent'});
        }
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    },
    async geteventmap(time) {
      try {
        this.loading = true;
        const response = await axios.get(`https://server.skydreamclub.cn/GetEventMapDetailByTime.php?time=${time}`);
        const data = response.data;

        this.waypoints = data.data.routrdetail.msg.waypoint;
        // Calculate the average longitude and latitude
        const totalCoordinates = this.waypoints.length;
        let totalLongitude = 0;
        let totalLatitude = 0;

        this.waypoints.forEach(waypoint => {
          totalLongitude += parseFloat(waypoint.lon);
          totalLatitude += parseFloat(waypoint.lat);
        });

        const averageLongitude = totalLongitude / totalCoordinates;
        const averageLatitude = totalLatitude / totalCoordinates;

        // Set the new center to the average coordinates
        const newCenter = [averageLongitude, averageLatitude];
        // Ensure the map is fully loaded before adding the waypoints line
        if (this.map.loaded()) {
          this.addWaypointsLine();
          this.addImageMarker([data.data.dep.msg.longitude_deg, data.data.dep.msg.latitude_deg], depImage);
          this.addImageMarker([data.data.app.msg.longitude_deg, data.data.app.msg.latitude_deg], appImage);
          this.map.flyTo({
            center: newCenter,
            zoom: 6, // Set the desired zoom level here
            essential: true // Set to true for better performance
          });
        } else {
          this.map.on('load', () => {
            this.addWaypointsLine();
            this.addImageMarker([data.data.dep.msg.longitude_deg, data.data.dep.msg.latitude_deg], depImage);
            this.addImageMarker([data.data.app.msg.longitude_deg, data.data.app.msg.latitude_deg], appImage);
            this.map.flyTo({
              center: newCenter,
              zoom: 6, // Set the desired zoom level here
              essential: true // Set to true for better performance
            });
          });
        }
      } catch (error) {
        console.error('Error fetching data:', error);
      } finally {
        this.load_main = false;
      }
    },
    gotodetail(id) {
      this.$router.push({name: 'eventdetail', params: {id: id}});
    },
    addImageMarker(coordinates, imageUrl) {
      // Load the image and add it as a marker
      this.map.loadImage(imageUrl, (error, image) => {
        if (error) {
          console.error('Error loading image:', error);
          return;
        }

        const imageId = `custom-marker-${coordinates.join('-')}`;

        // Check if the image is already added to avoid duplicates
        if (!this.map.hasImage(imageId)) {
          this.map.addImage(imageId, image);
        }

        // Add a layer with the image
        this.map.addLayer({
          id: `marker-${coordinates.join('-')}`,
          type: 'symbol',
          source: {
            type: 'geojson',
            data: {
              type: 'FeatureCollection',
              features: [
                {
                  type: 'Feature',
                  geometry: {
                    type: 'Point',
                    coordinates: coordinates, // Position of the marker
                  },
                },
              ],
            },
          },
          layout: {
            'icon-image': imageId,
            'icon-size': 0.1, // Adjust the size of the marker as needed
          },
        });
      });
    },
    addWaypointsLine() {

      const coordinates = this.waypoints.map(wp => [parseFloat(wp.lon), parseFloat(wp.lat)]);

      const geojson = {
        type: 'Feature',
        geometry: {
          type: 'LineString',
          coordinates: coordinates,
        },
      };

      if (this.map && this.map.getSource('waypoints')) {
        this.map.getSource('waypoints').setData(geojson);
      } else {
        this.map.addSource('waypoints', {
          type: 'geojson',
          data: geojson,
        });

        this.map.addLayer({
          id: 'waypoints',
          type: 'line',
          source: 'waypoints',
          layout: {
            'line-join': 'round',
            'line-cap': 'round',
          },
          paint: {
            'line-color': '#00cfff',
            'line-width': 6,
          },
        });
      }
    },
    openpast() {
      this.$router.push({name: 'list'});
    }
  }
};
</script>

<style>
.container {
  position: relative;
  width: 120%;
  height: 100vh;
}

.map-container {
  width: 100%;
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
  width: 300px; /* Adjust card container width as needed */
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
