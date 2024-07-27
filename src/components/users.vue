<script>


import { useRoute } from "vue-router";
import axios from "axios";

export default {
  data() {
    return {
      receivedId: null,
      usernum: null,
      userqqimage: 'https://server.skydreamclub.cn/atc.png',
      usergrade: null,
      usergradetext: '加载中...',
      userqq: '未填写',
      flighttime: 0,
      atctime: 0,
      onlinenum: 0,
      load: true,
      main: false,
      atcimage: null,
      filghtimage: null,
      flightdata: null,
      atcdata: null,
      integral: 0,
    };
  },

  methods: {
    getuserdata() {
      axios.get('https://server.skydreamclub.cn/GetUserByID.php?user_num=' + this.receivedId)
          .then(response => {

            const data = response.data
            console.log(data)
            if (data.message == '0 results') {
              this.$router.push({name: 'nouser'});
            } else {
              this.usergrade = data.user_grade;
              this.userqq = data.user_qq;
              this.getqqimage();
              this.usergradetext = this.getgrade();
            }
          })
          .catch(error => {
            console.error('Error fetching data:', error)
          })
    },
    getqqimage() {
      if (this.userqq != '未填写') {
        console.log(1);
        this.userqqimage = 'http://q.qlogo.cn/headimg_dl?dst_uin=' + this.userqq + '&spec=640&img_type=jpg';
      } else {
        this.userqqimage = 'https://server.skydreamclub.cn/atc.png';
      }
      console.log(this.userqqimage);
    },
    getgrade() {
      switch (this.usergrade) {
        case '2':
          return 'STU1';
        case '3':
          return 'STU2';
        case '4':
          return 'STU3';
        case '5':
          return 'CTR1';
        case '6':
          return 'CTR2';
        case '7':
          return 'CTR3';
        case '8':
          return 'INS1';
        case '9':
          return 'INS2';
        case '10':
          return 'INS3';
        case '11':
          return 'SUP';
        case '12':
          return 'ADM';
        default:
          return 'Pilot/OBS';
      }
    },
    GetUserTime() {
      axios.get('https://server.skydreamclub.cn/GetUserData.php?callsign=' + this.usernum)
          .then(response => {

            const data = response.data

            var flightTimeInSeconds = data.online_time.flight;
            this.getflightgradeimage(flightTimeInSeconds);
            this.getIntegral(flightTimeInSeconds);
            var flightTimeInHours = flightTimeInSeconds / 3600;
            this.flighttime = flightTimeInHours.toFixed(1) + " 小时";
            var atcTimeInSeconds = data.online_time.atc;
            var atcTimeInHours = atcTimeInSeconds / 3600;
            this.atctime = atcTimeInHours.toFixed(1) + " 小时";

          })
          .catch(error => {
            console.error('Error fetching data:', error)
          })
      axios.get('https://server.skydreamclub.cn/GetUserFlightAtcDataByID.php?callsign=' + this.usernum)
          .then(response => {

            const data = response.data

            var num = data.data.atcData.length + data.data.flightData.length;
            this.onlinenum = num.toString() + ' 次';


          })
          .catch(error => {
            console.error('Error fetching data:', error)
          })

    },
    async getdetaildata() {
      axios.get('https://server.skydreamclub.cn/GetUserFlightAtcDataByID.php?callsign=' + this.usernum)
          .then(response => {

            const data = response.data
            this.flightdata = data.data.flightData;
            this.atcdata = data.data.atcData;
            this.load = false;
            this.main = true;
          })
          .catch(error => {
            console.error('Error fetching data:', error)
          })
    },
    async getflightgradeimage(time) {
      console.log(time);
      try {
        const requestData = {
          token: 'ab321818',
          cid: this.usernum,
          time: time
        };

        const response = await axios.post(`https://server.skydreamclub.cn/GetUserFlightImage.php`, requestData);

        const data = response.data;
        console.log(data.image)
        this.filghtimage = data.image;
      } catch (error) {
        console.error('Error fetching event data:', error);
      }
    },
    async getflightatcimage() {
      try {
        const requestData = {
          token: 'ab321818',
          cid: this.usernum,
        };

        const response = await axios.post(`https://server.skydreamclub.cn/GetUserATCImage.php`, requestData);

        const data = response.data;
        console.log(data);
        console.log(data.image)
        this.atcimage = data.image;
      } catch (error) {
        console.error('Error fetching event data:', error);
      }
    },
    async getIntegral(time) {
      try {
        const response = await axios.get(
            "https://server.skydreamclub.cn/GetUserIntegralData.php",
            {
              params: {
                callsign: this.usernum,
                time: time,
              },
            }
        );
        const data = response.data;
        console.log(data);
        this.integral = data.integral;
        this.Hourlywages = data.Hourlywages;
      } catch (error) {
        console.error("Error fetching event data:", error);
      }
    },
    formatTime(seconds) {
      let hours = Math.floor(seconds / 3600);
      let minutes = Math.floor((seconds % 3600) / 60);
      let secs = seconds % 60;
      return `${hours}小时${minutes}分钟${secs}秒`;
    },
  },

  mounted() {
    const route = useRoute();
    const id = route.params.id;
    if (id) {
      this.receivedId = id;
      this.usernum = id;
      this.getuserdata();
      this.getdetaildata();
      this.GetUserTime();
      this.getqqimage();
      this.usergradetext = this.getgrade();
      this.getflightatcimage();
    } else {
      console.error('Invalid id:', id);
    }
  },
};
</script>

<template>
  <div v-show="load" style="text-align: center; padding-top: 100px; padding-right: 150px; padding-left: 150px"
       class="display">
    <el-card>
      <h1 style="font-size: 40px">正在加载数据,请稍等片刻</h1>
      <h1 style="font-size: 40px">waiting...</h1>
    </el-card>
  </div>
  <transition name="el-fade-in-linear">
    <div v-show="main">
      <el-card
          style="text-align: center;background: linear-gradient(90deg, #86d0d1,#009eff,#1134ff); display: flex; flex-direction: column; align-items: center;">
        <br><br>
        <el-avatar :src=userqqimage :size="130"/>
        <h1 style="font-size: 25px;color: #ffffff">{{ receivedId }} | {{ usergradetext }}</h1><br>
        <div style="display: flex; align-items: center; justify-content: center; color: #ffffff;font-size: 30px;">
          <img :src=filghtimage style="width: 150px; margin-right: 5px;" alt="">
          <span>|</span>
          <img :src=atcimage style="width: 150px; margin-left: 5px;" alt="">
        </div>
      </el-card>
      <br>
      <el-row :gutter="20" class="dashboard">
        <el-col :span="8">
          <div class="grid-content ep-bg-purple"/>
          <el-card class="rounded-card" style="background:#1F91DC;color: #ffffff;font-size: 35px">
            <span class="num">{{ flighttime }}</span><br>
            <span class="title-2">飞行时长</span>
          </el-card>
        </el-col>
        <el-col :span="8">
          <div class="grid-content ep-bg-purple"/>
          <el-card class="rounded-card" style="background: #009eff;color: #ffffff;font-size: 35px">
            <span class="num">{{ atctime }}</span><br>
            <span class="title-2"> 管制时长</span>
          </el-card>
        </el-col>
        <el-col :span="8">
          <div class="grid-content ep-bg-purple"/>
          <el-card class="rounded-card" style="background: #1CBCE8;color: #ffffff;font-size: 35px">
            <span class="num">{{ integral }} 元</span><br>
            <span class="title-2">天梦财富</span>
          </el-card>
        </el-col>
      </el-row>
      <br>
      <el-card><br>
        <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px">
          <el-icon class="icon-1">
            <position/>
          </el-icon>
          飞行记录
        </el-header>
        <br>
        <el-table :data="flightdata" border style="width: 100%; height: 250px">
          <el-table-column prop="client_name" label="呼号"/>
          <el-table-column prop="depairport" label="起飞机场"/>
          <el-table-column prop="destairport" label="落地机场"/>
          <el-table-column prop="aircraft" label="机型"/>
          <el-table-column prop="time" label="飞行时间"/>
          <el-table-column prop="online_time" label="飞行时长">
            <template v-slot="scope">
              {{ formatTime(scope.row.online_time) }}
            </template>
          </el-table-column>
        </el-table>
      </el-card>
      <br>
      <el-card><br/>
        <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px">
          <el-icon class="icon-1">
            <service/>
          </el-icon>
          管制记录
        </el-header>
        <br/>
        <el-table :data="atcdata" border style="width: 100%; height: 226px">
          <el-table-column prop="callsign" label="席位"/>
          <el-table-column prop="frequency" label="频率"/>
          <el-table-column prop="logon_time" label="管制时间"/>
          <el-table-column prop="online_time" label="管制时长">
            <template v-slot="scope">
              {{ formatTime(scope.row.online_time) }}
            </template>
          </el-table-column>
        </el-table>
      </el-card>
    </div>
  </transition>
</template>

<style scoped>
.el-card:hover {
  transform: translateY(-10px);
  /* Move the card upwards by 20px */
  transition: transform 0.5s ease;
  /* Apply a smooth transition */
}
</style>