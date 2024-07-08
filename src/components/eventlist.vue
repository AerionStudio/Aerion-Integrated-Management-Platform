<template>
  <div v-show="load" style="text-align: center; padding-top: 100px; padding-right: 150px; padding-left: 150px"
       class="display">
    <el-card>
      <h1 style="font-size: 40px">正在加载数据,请稍等片刻</h1>
      <h1 style="font-size: 40px">waiting...</h1>
    </el-card>
  </div>
  <transition name="el-fade-in-linear">
    <el-card v-show="main">
      <el-loading :visible="loading">
        <el-table :data="eventsData" height="80vh" style="width: 100%;">
          <el-table-column prop="dep_icao" label="起飞机场(DEP)" width="180"/>
          <el-table-column prop="app_icao" label="落地机场(APP)" width="180"/>
          <el-table-column prop="time" label="活动时间(TIME)" width="180"/>
          <el-table-column prop="state" label="状态(STATE)">
            <template v-slot="{ row }">
              <el-tag :type="getButtonType(row.state)" :disabled="true" size="small">
                {{ getButtonLabel(row.state) }}
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column fixed="right" label="操作" width="120">
            <template v-if="showButton" v-slot="{ row }">
              <el-button type="primary" round @click="handleButtonClick(row.time)" size="small">
                查看
              </el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-loading>
    </el-card>
  </transition>
</template>

<script>
import {ref, onMounted} from 'vue';
import axios from 'axios';
import {useRouter} from 'vue-router';

export default {
  name: 'EventsComponent',

  data() {
    return {
      eventsData: [],
      showButton: true,
      loading: false,
      load: true,
      main: false
    };
  },

  mounted() {
    this.loadData();
  },

  methods: {
    async loadData() {
      try {
        this.loading = true;
        const response = await axios.post('https://server.skydreamclub.cn/GetEvents.php');
        this.eventsData = response.data;
      } catch (error) {
        console.error('Error fetching data:', error);
      } finally {
        this.load = false;
        this.main = true;
      }
    },

    getButtonType(state) {
      switch (state) {
        case '1':
          return 'success';
        case '2':
          return 'primary';
        case '3':
          return 'warning';
        case '4':
          return 'danger';
        default:
          return 'default';
      }
    },

    getButtonLabel(state) {
      switch (state) {
        case '1':
          return '正在报名';
        case '2':
          return '正在进行';
        case '3':
          return '已结束';
        case '4':
          return '已取消';
        default:
          return state;
      }
    },

    handleButtonClick(id) {
      this.$router.push({name: 'eventdetail', params: {id: id}});
    },
  },
};
</script>

<style scoped>
/* Add your custom styles here */
</style>
