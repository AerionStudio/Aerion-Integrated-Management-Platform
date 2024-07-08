<script>
import {Place} from "@element-plus/icons-vue";
import mapboxgl from "mapbox-gl";
import depImage from "../assets/dep.png";
import appImage from "../assets/app.png";

export default {
  components: {Place},
  data() {
    return {
      routedata: [],
      dep_icao: null,
      app_icao: null,
      load: false,
      display: false,
      map: null,
      waypoints: [],
      sid: [],
      star: [],
    };
  },
  methods: {
    showSuccessNotification(message) {
      this.$notify({
        title: 'Success',
        message: message,
        type: 'success',
      });
    },
    showErrorNotification(message) {
      this.$notify({
        title: 'Error',
        message: message,
        type: 'error',
      });
    },
    getweather() {
      this.map = null;
      this.routedata = [];
      this.load = true;
      this.display = false;
      const dep_icao = this.dep_icao;
      const app_icao = this.app_icao;
      const data = {
        token: 'ab321818',
        dep: dep_icao,
        app: app_icao
      };

      // Send data using POST request
      fetch('https://server.skydreamclub.cn/GetRoute.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
          .then(response => response.json())
          .then(data => {
            console.log(data);
            this.load = false;
            this.display = true;
            this.routedata = data.data;
            this.waypoints = data.data.routrdetail.msg.waypoint;
            this.sid = data.data.route.msg.sid;
            this.star = data.data.route.msg.star;

            // Transforming the sid array to a format suitable for el-table
            const formattedDatasid = this.sid.map(item => ({
              date: item[1],  // Runway
              name: item[0]   // Procedure
            }));

            // Assigning the formatted data to this.sid
            this.sid = formattedDatasid;

            // Transforming the star array to a format suitable for el-table
            const formattedDatastar = this.star.map(item => ({
              date: item[1],  // Runway
              name: item[0]   // Procedure
            }));

            // Assigning the formatted data to this.star
            this.star = formattedDatastar;

            this.$nextTick(() => {
              if (!this.map) {
                this.initmap();
              }
              this.updateMap(data);
            });
          })
          .catch((error) => {
            console.error('Error:', error.message);
            this.load = false;
            this.showErrorNotification('查询失败，请重试。');
          });
    },
    initmap() {
      mapboxgl.accessToken = 'pk.eyJ1IjoiZ3VvYXI3c2J2ZCIsImEiOiJjbG9wd2s5dncwMThxMnFtazNncGxoZ3VyIn0.kxcLHaYXSoM8GfPpurqU7w';
      this.map = new mapboxgl.Map({
        container: this.$refs.mapContainer,
        style: 'mapbox://styles/guoar7sbvd/clwxpl9tc012w01rb4y28frgj',
        center: [116.397428, 39.90923],
        zoom: 3
      });
    },
    updateMap(data) {
      const totalCoordinates = this.waypoints.length;
      let totalLongitude = 0;
      let totalLatitude = 0;

      this.waypoints.forEach(waypoint => {
        totalLongitude += parseFloat(waypoint.lon);
        totalLatitude += parseFloat(waypoint.lat);
      });

      const averageLongitude = totalLongitude / totalCoordinates;
      const averageLatitude = totalLatitude / totalCoordinates;
      const newCenter = [averageLongitude, averageLatitude];

      const onMapLoad = () => {
        this.addWaypointsLine();
        this.addImageMarker([data.data.dep.msg.longitude_deg, data.data.dep.msg.latitude_deg], depImage);
        this.addImageMarker([data.data.app.msg.longitude_deg, data.data.app.msg.latitude_deg], appImage);
        this.map.flyTo({
          center: newCenter,
          zoom: 4,
          essential: true
        });
      };

      if (this.map.loaded()) {
        onMapLoad();
      } else {
        this.map.on('load', onMapLoad);
      }
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

      if (this.map.getSource('waypoints')) {
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
    addImageMarker(coordinates, imageUrl) {
      this.map.loadImage(imageUrl, (error, image) => {
        if (error) {
          console.error('Error loading image:', error);
          return;
        }

        const imageId = `custom-marker-${coordinates.join('-')}`;

        if (!this.map.hasImage(imageId)) {
          this.map.addImage(imageId, image);
        }

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
                    coordinates: coordinates,
                  },
                },
              ],
            },
          },
          layout: {
            'icon-image': imageId,
            'icon-size': 0.1,
          },
        });
      });
    },
  },
};
</script>

<template>
  <div class="main">
    <transition name="el-fade-in-linear">
      <el-card><br>
        <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
          <el-icon class="icon-1">
            <place/>
          </el-icon>
          航路查询
        </el-header>
        <br>
        <el-form-item label="起飞机场ICAO" prop="icon">
          <el-input v-model="dep_icao" placeholder="请输入机场ICAO"></el-input>
        </el-form-item>
        <el-form-item label="落地机场ICAO" prop="icon">
          <el-input v-model="app_icao" placeholder="请输入机场ICAO"></el-input>
        </el-form-item>
        <br>
        <el-button type="primary" size="default" style="width: 100%" @click="getweather">查询</el-button>
      </el-card>
    </transition>
    <br>
    <el-card v-if="load" style="text-align: center">
      <h1 style="font-size: 40px">正在加载数据,请稍等片刻</h1>
      <h1 style="font-size: 40px">waiting...</h1>
    </el-card>
    <div v-if="display">
      <transition name="el-fade-in-linear">
        <el-card>
          <br>
          <el-header class="el-header" style="margin-bottom: 10px; font-size: 30px;">
            <el-icon class="icon-1">
              <Position/>
            </el-icon>
            {{ routedata.dep.msg.cn_name }} ~ {{ routedata.app.msg.cn_name }}
            / {{ routedata.dep.msg.gps_code }} ~ {{ routedata.app.msg.gps_code }}
          </el-header>
          <br>
          <div ref="mapContainer" class="map-container"></div>
          <br><br>
          <el-tabs tab-position="right" style="height: 100%" class="demo-tabs">
            <el-tab-pane label="航路 & 程序">
              <el-tag type="primary" size="large" style="padding: 20px"><h1 style="font-size: 25px">
                {{ routedata.dep.msg.gps_code }} ✈ {{ routedata.app.msg.gps_code }}
                全程{{ routedata.routrdetail.msg.distance }}海里
              </h1></el-tag>
              <br><br>
              <div>
                <h1 style="font-size: 25px;color: #85ce61">飞行航路:
                  {{ routedata.route.msg.route }}</h1>
              </div>
              <br><br>
              <el-row :gutter="20">
                <el-col :span="12">
                  <div class="grid-content ep-bg-purple"/>
                  <el-card>
                    <h1> 推荐{{ routedata.dep.msg.gps_code }}离场程序 </h1>
                    <br>
                    <el-table :data="sid" border style="width: 100%">
                      <el-table-column prop="date" label="跑道"/>
                      <el-table-column prop="name" label="程序"/>
                    </el-table>
                  </el-card>
                </el-col>
                <el-col :span="12">
                  <div class="grid-content ep-bg-purple"/>
                  <el-card>
                    <h1> 推荐{{ routedata.app.msg.gps_code }}进场程序</h1>
                    <br>
                    <el-table :data="star" border style="width: 100%">
                      <el-table-column prop="date" label="跑道"/>
                      <el-table-column prop="name" label="程序"/>
                    </el-table>
                  </el-card>
                </el-col>
              </el-row>
            </el-tab-pane>
            <el-tab-pane label="机场天气"><br>
              <br>
              <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
                <el-icon class="icon-1">
                  <wind-power/>
                </el-icon>
                {{ routedata.dep.msg.gps_code  }} 天气 - - - (起飞机场)
              </el-header>
              <div class="weather">
                <h1>METAR </h1>
                <h3>{{ routedata.dep_weather.msg.metar }}</h3>
                <br/>
                <h2 style="color: #b25252">发布时间: {{ routedata.dep_weather.msg.metar_decode.time }}</h2>
                <h2>
                  风况: {{ routedata.dep_weather.msg.metar_decode.wind_dir }}°{{
                    routedata.dep_weather.msg.metar_decode.wind_speed
                  }}{{ routedata.dep_weather.msg.metar_decode.wind_unit }}
                </h2>
                <h2>
                  能见度: {{
                    routedata.dep_weather.msg.metar_decode.visibility
                  }}{{ routedata.dep_weather.msg.metar_decode.visibility_unit }}
                </h2>
                <h2>修正海压: {{ routedata.dep_weather.msg.metar_decode.qnh }}{{
                    routedata.dep_weather.msg.metar_decode.qnh_unit
                  }}</h2>
                <h2>温度: {{ routedata.dep_weather.msg.metar_decode.temperature }} °C</h2>
                <h2>露点温度: {{ routedata.dep_weather.msg.metar_decode.dewpoint }} °C</h2>
                <h2>预报: {{ routedata.dep_weather.msg.metar_decode.forcast }}</h2>
                <br/>
                <h1>TAF </h1>
                <h3>{{ routedata.dep_weather.msg.taf }}</h3>
              </div>
              <br/>
              <hr/>
              <br/>
              <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
                <el-icon class="icon-1">
                  <wind-power/>
                </el-icon>
                {{ routedata.app.msg.gps_code  }} 天气 - - - (落地机场)
              </el-header>
              <div class="weather">
                <h1>METAR </h1>
                <h3>{{ routedata.app_weather.msg.metar }}</h3>
                <br/>
                <h2 style="color: #b25252">发布时间: {{ routedata.app_weather.msg.metar_decode.time }}</h2>
                <h2>
                  风况: {{ routedata.app_weather.msg.metar_decode.wind_dir }}°{{
                    routedata.app_weather.msg.metar_decode.wind_speed
                  }}{{ routedata.app_weather.msg.metar_decode.wind_unit }}
                </h2>
                <h2>
                  能见度: {{
                    routedata.app_weather.msg.metar_decode.visibility
                  }}{{ routedata.app_weather.msg.metar_decode.visibility_unit }}
                </h2>
                <h2>修正海亚: {{ routedata.app_weather.msg.metar_decode.qnh }}{{
                    routedata.app_weather.msg.metar_decode.qnh_unit
                  }}</h2>
                <h2>温度: {{ routedata.app_weather.msg.metar_decode.temperature }} °C</h2>
                <h2>露点温度: {{ routedata.app_weather.msg.metar_decode.dewpoint }} °C</h2>
                <h2>预报: {{ routedata.app_weather.msg.metar_decode.forcast }}</h2>
                <br/>
                <h1>TAF </h1>
                <h3>{{ routedata.app_weather.msg.taf }}</h3>
              </div>
            </el-tab-pane>
          </el-tabs>
          <br>
          <h3>导航数据版本: {{ routedata.route.msg.database }}</h3>
        </el-card>
      </transition>
    </div>
  </div>
</template>

<style scoped>
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

.map-container {
  width: 100%;
  height: 500px;
}

.el-card:hover {
  transform: translateY(-10px);
  transition: transform 0.5s ease;
}

.weather {
  margin-left: 30px;
}

.weather h1 {
  font-size: 35px;
}

.weather h2 {
  font-size: 25px;
}

.weather h3 {
  font-size: 20px;
  color: #1CBCE8;
}
</style>
