<template>
  <div v-show="load" style="text-align: center; padding-top: 100px; padding-right: 150px; padding-left: 150px"
       class="display">
    <loading></loading>
  </div>
  <transition name="el-fade-in-linear">
    <div v-show="main">
      <el-row :gutter="20" class="radius">
        <el-col :span="15">
          <div class="grid-content ep-bg-purple"/>
          <el-header class="el-header">
            <el-icon class="icon">
              <DataAnalysis/>
            </el-icon>
            个人仪表板
          </el-header>
          <el-card class="callsign rounded-card"
                   style="background: linear-gradient(90deg, #86d0d1,#009eff,#1134ff); padding: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
              <el-avatar :size="120" :src="image"
                         class="avatar"/>
              <span >       #SCF{{ user_num }}</span>
              <span style="display: flex; align-items: center;">
                <img :src="filghtimage" style="width: 150px; margin-right: 5px;" alt="">
                <span>|</span>
                <img :src="atcimage" style="width: 150px; margin-left: 5px;" alt="">
              </span>
            </div>
          </el-card>


          <br>
          <el-row :gutter="20" class="dashboard">
            <el-col :span="8">
              <div class="grid-content ep-bg-purple"/>
              <el-card class="rounded-card" style="background:#1F91DC;">
                <span class="num">{{ flighttime }}</span><br>
                <span class="title-2">飞行时长</span>
              </el-card>
            </el-col>
            <el-col :span="8">
              <div class="grid-content ep-bg-purple"/>
              <el-card class="rounded-card" style="background: #009eff;">
                <span class="num">{{ atctime }}</span><br>
                <span class="title-2"> 管制时长</span>
              </el-card>
            </el-col>
            <el-col :span="8">
              <div class="grid-content ep-bg-purple"/>
              <el-card class="rounded-card" style="background: #1CBCE8;">
                <span class="num">{{ onlinenum }}</span><br>
                <span class="title-2">连线次数</span>
              </el-card>
            </el-col>
          </el-row>
          <br>
          <el-header class="el-header">
            <el-icon class="icon">
              <DataAnalysis/>
            </el-icon>
            平台仪表板
          </el-header>
          <el-card class="rounded-card">
            <div>
              <el-row>
                <el-col :span="6">
                  <el-statistic title="注册用户" :value="countUser"/>
                </el-col>
                <el-col :span="6">
                  <el-statistic :value="countAtc">
                    <template #title>
                      <div style="display: inline-flex; align-items: center">
                        注册管制
                      </div>
                    </template>
                  </el-statistic>
                </el-col>
                <el-col :span="6">
                  <el-statistic title="活动次数" :value="countEvent"/>
                </el-col>
                <el-col :span="6">
                  <el-statistic title="在线用户" :value="onlineAll">
                  </el-statistic>
                </el-col>
              </el-row>
            </div>


          </el-card>
        </el-col>
        <el-col :span="9">
          <div class="grid-content ep-bg-purple"/>
          <el-header class="el-header">
            <el-icon class="icon">
              <Promotion/>
            </el-icon>
            时长排行榜
          </el-header>
          <el-card class="rounded-card "> <!-- 添加一个新的类名 -->
            <el-table :data="tableData" style="width: 100%" height="470">
              <el-table-column fixed prop="rank" label="排序" width="110"/>
              <el-table-column prop="CID" label="用户" width="190">
                <template v-slot="scope">
                  <el-tooltip
                      class="box-item"
                      effect="dark"
                      :content="'查看 ' + scope.row.CID + ' 个人信息'"
                      placement="top"

                  >
                    <span @click="watchuser(scope.row.CID)">{{ scope.row.CID }}</span>
                  </el-tooltip>
                </template>
              </el-table-column>
              <el-table-column prop="Online_Time_Hours" label="时长" width="190"/>
            </el-table>
          </el-card>
        </el-col>
      </el-row>
      <br>
      <el-header class="el-header">
        <el-icon class="icon">
          <Calendar/>
        </el-icon>
        近期活动
      </el-header>

      <el-tooltip
          class="box-item"
          effect="dark"
          :content="'点击前往报名' + eventtime + '日的活动'"
          placement="top"
      >
        <el-card class="rounded-card" @click="gotoevent(eventtime)">
          <el-descriptions
              class="margin-top"
              title="活动信息"
              :column="3"
              :size="size"
              border
          >
            <el-descriptions-item>
              <template #label>
                <div class="cell-item">
                  活动时间
                </div>
              </template>
              {{ eventtime }}
            </el-descriptions-item>
            <el-descriptions-item>
              <template #label>
                <div class="cell-item">
                  起飞机场(DEP)
                </div>
              </template>
              {{ dep }}
            </el-descriptions-item>
            <el-descriptions-item>
              <template #label>
                <div class="cell-item">
                  落地机场(APP)
                </div>
              </template>
              {{ app }}
            </el-descriptions-item>
            <el-descriptions-item>
              <template #label>
                <div class="cell-item">
                  活动状态
                </div>
              </template>
              <el-tag size="small" :type="stateType">{{ state }}</el-tag>
            </el-descriptions-item>

            <el-descriptions-item>
              <template #label>
                <div class="cell-item">
                  活动航路
                </div>
              </template>
              {{ route }}
            </el-descriptions-item>
          </el-descriptions>


        </el-card>
      </el-tooltip>

      <br>
      <el-tooltip
          class="box-item"
          effect="dark"
          :content="'点击前往报名' + eventtimeSecond + '日的活动'"
          placement="top"
      >
        <el-card class="rounded-card" @click="gotoevent(eventtimeSecond)">
          <el-descriptions
              class="margin-top"
              title="活动信息"
              :column="3"
              :size="size"
              border
          >
            <el-descriptions-item>
              <template #label>
                <div class="cell-item">
                  活动时间
                </div>
              </template>
              {{ eventtimeSecond }}
            </el-descriptions-item>
            <el-descriptions-item>
              <template #label>
                <div class="cell-item">
                  起飞机场(DEP)
                </div>
              </template>
              {{ depSecond }}
            </el-descriptions-item>
            <el-descriptions-item>
              <template #label>
                <div class="cell-item">
                  落地机场(APP)
                </div>
              </template>
              {{ appSecond }}
            </el-descriptions-item>
            <el-descriptions-item>
              <template #label>
                <div class="cell-item">
                  活动状态
                </div>
              </template>
              <el-tag size="small" :type="stateTypeSecond">{{ stateSecond }}</el-tag>
            </el-descriptions-item>

            <el-descriptions-item>
              <template #label>
                <div class="cell-item">
                  活动航路
                </div>
              </template>
              {{ routeSecond }}
            </el-descriptions-item>
          </el-descriptions>


        </el-card>
      </el-tooltip>

    </div>
  </transition>
</template>


<script>
import {ChatLineRound, Clock, Male} from '@element-plus/icons-vue'
import axios from 'axios'
import Loading from "./loading.vue"; // 引入axios库

export default {
  components: {Loading, Clock},
  data() {
    return {
      tableData: [],
      user_num: sessionStorage.getItem("user_num"),
      countUser: '加载中...',
      countAtc: '加载中...',
      countEvent: '加载中...',
      onlineAll: '加载中...',
      eventtime: ' ',
      dep: ' ',
      app: ' ',
      state: ' ',
      route: ' ',
      stateType: ' ',
      eventtimeSecond: ' ',
      depSecond: ' ',
      appSecond: ' ',
      stateSecond: ' ',
      routeSecond: ' ',
      stateTypeSecond: ' ',
      flighttime: ' ',
      atctime: ' ',
      onlinenum: ' ',
      load: true,
      main: false,
      filghtimage: null,
      atcimage: null,
      image:'',
      userqq:sessionStorage.getItem("user_qq")
    }
  },
  computed: {
    animatedOutputValue() {
      return outputValue.value
    },
  },
  methods: {
    watchuser(id) {
      this.$router.push({name: 'users', params: {id: id}});
    },
    fetchData() {
      axios.get('https://server.skydreamclub.cn/GetFlightTimeList.php')
          .then(response => {
            // 检查response.data是否存在并且不为空数组
            if (response.data && Array.isArray(response.data) && response.data.length > 0) {
              const data = response.data
              // 计算rank
              data.forEach((item, index) => {
                item.rank = (index + 1).toString()
              })
              this.tableData = data
            } else {
              console.error('Invalid data format:', response.data)
            }
          })
          .catch(error => {
            console.error('Error fetching data:', error)
          })
    },
    getplatformdata() {
      axios.post('https://server.skydreamclub.cn/GetPlatformData.php')
          .then(response => {

            const data = response.data
            this.countUser = parseInt(data.count_user, 10);
            this.countAtc = parseInt(data.count_atc, 10);
            this.countEvent = parseInt(data.count_event, 10);
            this.onlineAll = parseInt(data.online_all, 10);
          })
          .catch(error => {
            console.error('Error fetching data:', error)
          })
    },
    GetTheLeastEvent() {
      axios.post('https://server.skydreamclub.cn/GetEvents.php')
          .then(response => {

            const data = response.data

            this.eventtime = data[0].time;
            this.dep = data[0].dep_icao + ' / ' + data[0].dep;
            this.app = data[0].app_icao + ' / ' + data[0].app;
            this.route = data[0].route;
            switch (data[0].state) {
              case '1':
                this.state = "报名中";
                this.stateType = 'success'
                break;
              case '2':
                this.state = "正在进行";
                this.stateType = "primary"
                break;
              case '3':
                this.state = "已结束";
                this.stateType = 'warning'
                break;
              case '4':
                this.state = "已取消";
                this.stateType = "danger";
                break;
            }
          })
          .catch(error => {
            console.error('Error fetching data:', error)
          })
    },
    GetTheSecondEvent() {
      axios.post('https://server.skydreamclub.cn/GetEvents.php')
          .then(response => {

            const data = response.data

            this.eventtimeSecond = data[1].time;
            this.depSecond = data[1].dep_icao + ' / ' + data[1].dep;
            this.appSecond = data[1].app_icao + ' / ' + data[1].app;
            this.routeSecond = data[1].route;
            switch (data[1].state) {
              case '1':
                this.stateSecond = "报名中";
                this.stateTypeSecond = 'success'
                break;
              case '2':
                this.stateSecond = "正在进行";
                this.stateTypeSecond = "primary"
                break;
              case '3':
                this.stateSecond = "已结束";
                this.stateTypeSecond = 'warning'
                break;
              case '4':
                this.stateSecond = "已取消";
                this.stateTypeSecond = "danger";
                break;
            }
          })
          .catch(error => {
            console.error('Error fetching data:', error)
          })
    },
    GetUserTime() {
      axios.get('https://server.skydreamclub.cn/GetUserData.php?callsign=' + sessionStorage.getItem("user_num"))
          .then(response => {

            const data = response.data

            var flightTimeInSeconds = data.online_time.flight;
            this.getflightgradeimage(flightTimeInSeconds);
            var flightTimeInHours = flightTimeInSeconds / 3600;
            this.flighttime = flightTimeInHours.toFixed(1) + " 小时";
            var atcTimeInSeconds = data.online_time.atc;
            var atcTimeInHours = atcTimeInSeconds / 3600;
            this.atctime = atcTimeInHours.toFixed(1) + " 小时";

          })
          .catch(error => {
            console.error('Error fetching data:', error)
          })
      axios.get('https://server.skydreamclub.cn/GetUserFlightAtcDataByID.php?callsign=' + sessionStorage.getItem("user_num"))
          .then(response => {

            const data = response.data

            var num = data.data.atcData.length + data.data.flightData.length;
            this.onlinenum = num.toString() + ' 次';
            this.load = false;
            this.main = true;

          })
          .catch(error => {
            console.error('Error fetching data:', error)
          })

    },
    gotoevent(id) {
      this.$router.push({name: 'eventdetail', params: {id: id}});
    },
    async getflightgradeimage(time) {
      console.log(time);
      try {
        const requestData = {
          token: 'ab321818',
          cid: sessionStorage.getItem("user_num"),
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
          cid: sessionStorage.getItem("user_num"),
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
    getqqimage(){
      if (this.userqq != '未填写') {
        console.log(1);
        this.image = 'http://q.qlogo.cn/headimg_dl?dst_uin=' + this.userqq + '&spec=640&img_type=jpg';
      } else {
        this.image = 'https://server.skydreamclub.cn/atc.png';
      }
    }

  },


  mounted() {
    // 在组件挂载后获取数据
    this.getflightatcimage();
    this.fetchData();
    this.getplatformdata();
    this.GetTheLeastEvent();
    this.GetTheSecondEvent();
    this.GetUserTime();
    this.getqqimage();

  },
}
</script>


<style scoped>
.rounded-card {
  cursor: pointer;
}

.el-card:hover {
  transform: translateY(-10px); /* Move the card upwards by 20px */
  transition: transform 0.5s ease; /* Apply a smooth transition */
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

.radius {
  border-radius: 20px;
}

.callsign span {
  color: #f9f9f9;
  font-size: 50px;
}

/* 新增的样式 */
.rounded-card {
  border-radius: 15px; /* 圆角大小 */
}

.title-2 {
  font-size: 22px;
  color: #f9f9f9;
}

.num {
  font-size: 45px;
  color: #f9f9f9;
}

.icon-dashboard {
  font-size: 40px;
  color: #f9f9f9;
  opacity: 0.7; /* 降低透明度 */
}

</style>
