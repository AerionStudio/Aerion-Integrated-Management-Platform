<template>
  <el-row :gutter="20">
    <el-col :span="8">
      <div class="grid-content ep-bg-purple"/>
      <el-card><br>
        <el-header class="el-header">
          <el-icon class="icon">
            <Picture/>
          </el-icon>
          航图查询
        </el-header>
      </el-card>
      <br>
      <el-card style="height: 500px; overflow: hidden;" v-if="selectedCharts">
        <el-menu
            class="el-menu-vertical-demo"
        >
          <el-menu-item-group title="图表">
            <el-menu-item
                v-for="(chart, index) in selectedCharts"
                :key="index"
                :index="index"
                @click="view(icao,chart.image_day)">
              {{ chart.name }}
            </el-menu-item>
          </el-menu-item-group>
        </el-menu>
      </el-card>
      <br>
      <el-card style="height: 500px; overflow: hidden;">
        <el-input
            placeholder="搜索机场 ICAO"
            v-model="searchQuery"
            class="search-input"
        />
        <el-menu
            class="el-menu-vertical-demo"
            @select="handleSelect">
          <el-menu-item-group title="机场">
            <el-menu-item
                v-for="item in filteredIcaoList"
                :key="item.ICAO"
                :index="item.ICAO">
              {{ item.ICAO }}
            </el-menu-item>
          </el-menu-item-group>
        </el-menu>
      </el-card>
      <br>
      <el-card style=" overflow: hidden;">
        <el-input
            placeholder="搜索未提供机场 ICAO"
            v-model="search_icao"
            class="search-input"
        />
        <el-button style="width: 100%" @click="search">查找</el-button>
      </el-card>
      <br>
    </el-col>
    <el-col :span="16">
      <div class="grid-content ep-bg-purple">
        <el-card v-if="image">
          <img :src="image" style="width: 100%;height:100%"/>
        </el-card>
      </div>
    </el-col>
  </el-row>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      icaoList: null,
      searchQuery: null,
      selectedCharts: null,
      icao: null,
      image: null,
      search_icao: null,
    };
  },
  computed: {
    filteredIcaoList() {
      if (!this.searchQuery) {
        return this.icaoList;
      }
      const lowerCaseQuery = this.searchQuery.toLowerCase();
      return this.icaoList.filter(item => item.ICAO.toLowerCase().includes(lowerCaseQuery));
    }
  },
  created() {
    this.fetchICAOData();
  },
  methods: {
    fetchICAOData() {
      axios.get('https://server.skydreamclub.cn/airports/all.php')
          .then(response => {
            this.icaoList = response.data;
          })
          .catch(error => {
            console.error('Error fetching ICAO data:', error);
          });
    },
    handleSelect(index) {
      this.image = null;
      this.selectedCharts = null;
      console.log('Selected ICAO:', index);
      this.callAPIByICAO(index);
    },
    callAPIByICAO(icao) {
      this.image = null;
      this.selectedCharts = null;
      const token = 'ab321818';
      const apiUrl = `https://server.skydreamclub.cn/GetChart.php`;

      axios.post(apiUrl, {token, icao})
          .then(response => {
            this.selectedCharts = response.data.chart.charts; // 假设返回的数据包含一个 "chart" 对象，内部有一个 "charts" 数组
            this.icao = response.data.airport.gps_code;
          })
          .catch(error => {
            console.error('Error calling API:', error);
          });
    },
    search() {
      this.image = null;
      this.selectedCharts = null;
      const token = 'ab321818';
      const apiUrl = `https://server.skydreamclub.cn/GetChart.php`;
      let icao = this.search_icao;
      axios.post(apiUrl, {token, icao})
          .then(response => {
            this.selectedCharts = response.data.chart.charts; // 假设返回的数据包含一个 "chart" 对象，内部有一个 "charts" 数组
            this.icao = response.data.airport.gps_code;
          })
          .catch(error => {
            console.error('Error calling API:', error);
          });
    },
    view(icao, image) {
      console.log(image);
      const token = 'ab321818';
      const apiUrl = `https://server.skydreamclub.cn/GetChartDownload.php`;

      axios.post(apiUrl, {token, icao, image})
          .then(response => {
            this.image = response.data.chart.image;
          })
          .catch(error => {
            console.error('Error calling API:', error);
          });
    }
  }
};
</script>

<style scoped>
.el-card:hover {
  transform: translateY(-10px);
  transition: transform 0.5s ease;
}

.el-header {
  margin-bottom: 10px;
  font-size: 30px;
}

.icon {
  position: relative;
}

.icon::after {
  content: "";
  position: absolute;
  bottom: -25px;
  left: 0;
  width: 350%;
  height: 6px;
  background-color: #66b1ff;
  border-radius: 10px;
}

.search-input {
  margin-bottom: 10px;
  width: 100%;
}
</style>
