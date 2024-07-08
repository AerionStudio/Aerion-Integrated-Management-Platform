<template>

  <div>
    <div v-show="load" style="text-align: center; padding-top: 100px; padding-right: 150px; padding-left: 150px"
         class="display">
      <el-card>
        <h1 style="font-size: 40px">正在加载数据,请稍等片刻</h1>
        <h1 style="font-size: 40px">waiting...</h1>
      </el-card>
    </div>

    <!-- Fade-in animation for main content -->
    <transition name="el-fade-in-linear">

      <div v-show="main">
        <el-dialog v-model="atc_view" title="管制报名" width="800">
          <div v-if="selectedAtc">
            <div style="text-align: center">
              <p>
                <el-tag v-if="selectedAtc.sta === 1 && selectedAtc.user.id===user_num" type="warning">您的席位</el-tag>
                <el-tag v-else-if="selectedAtc.sta === 1 " type="danger">席位激活</el-tag>
                <el-tag v-else type="success">席位空闲</el-tag>
              </p>
              <p>
                <el-tag type="primary" size="large">席位: {{ selectedAtc.atc }}</el-tag>
              </p>
              <p>
                <el-tag type="primary" size="large">频率: {{ selectedAtc.atc_fp }}</el-tag>
              </p>
              <p v-if="selectedAtc.user">
                <div v-if="selectedAtc.user&&selectedAtc.supervision">
                  <el-tag type="warning" size="large">管制人员: {{ selectedAtc.user.id }}(学员)</el-tag>
                </div>
                <div v-else-if="selectedAtc.user">
                  <el-tag type="primary" size="large">管制人员:{{ selectedAtc.user.id }}</el-tag>
                </div>
              </p>

              <div v-if="selectedAtc.supervision&&selectedAtc.supervision.id!='NULL'">
                <el-tag type="success">监管人员:{{
                    selectedAtc.supervision.id
                  }}
                </el-tag>
              </div>
              <div v-else-if="selectedAtc.supervision">
                <el-tag type="warning">监管人员:暂无监管</el-tag>
              </div>
              <br><br>
              <div>
                <el-button v-if="eventsData.state!=1" type="warning" disabled>已结束</el-button>
                <el-button v-else-if="selectedAtc.sta === 1&& selectedAtc.user.id===user_num" type="danger"
                           @click="cancelatc(selectedAtc.atc)">取消报名
                </el-button>
                <el-button v-else-if="selectedAtc.sta === 1" type="danger" disabled>不是您的席位</el-button>
                <el-button v-else-if="user_grade>1&&user_grade>=selectedAtc.atc_grade" type="success"
                           @click="signupatc(selectedAtc.atc)">报名
                </el-button>
                <el-button v-else type="danger" disabled>无权限</el-button>
              </div>
              <br>
              <div>
                <el-button v-if="eventsData.state!=1&&selectedAtc.supervision" type="warning" disabled>已结束
                </el-button>
                <el-button v-else-if="selectedAtc.supervision&&user_grade>=8&&selectedAtc.supervision.id==='NULL'"
                           type="primary"
                           @click="signupsupatc(selectedAtc.atc)">报名监管
                </el-button>
                <el-button v-else-if="selectedAtc.supervision&&selectedAtc.supervision.id===user_num" type="primary"
                           @click="cancelatcsup(selectedAtc.atc)">
                  取消监管报名
                </el-button>
                <el-button v-else-if="selectedAtc.supervision" type="danger" disabled>不是您监管的学员</el-button>
              </div>
            </div>


            <br><br>
            <div v-if="selectedAtc.sta === 1">
              <div class="header-container">
                <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
                  <el-icon class="icon-1">
                    <alarm-clock/>
                  </el-icon>
                  候补名单
                </el-header>
                <el-button v-if="eventsData.state!=1&&selectedAtc.atc" type="warning" style="margin-left: auto;"
                           disabled
                >
                  已结束
                </el-button>
                <el-button v-else-if="selectedAtc.user.id===user_num" type="warning" style="margin-left: auto;" disabled
                >
                  不可以候补自己的席位
                </el-button>
                <el-button v-else-if="atc_wait_list_sta==='200'" type="success" style="margin-left: auto;"
                           @click="signupatcwaitlist(selectedAtc.atc)"
                >
                  + 候补
                </el-button>
                <el-button v-else type="danger" style="margin-left: auto;"
                           @click="cancelatcwaitlist(selectedAtc.atc)"
                >
                  x 取消候补
                </el-button>
              </div>
              <br>
              <el-table :data="atc_wait_list" border style="width: 100%">
                <el-table-column prop="cid" label="用户"/>
              </el-table>
            </div>

          </div>
        </el-dialog>
        <el-dialog v-model="pilot_login" title="机组报名" width="500" align-center class="custom-dialog">
          <template #footer>
            <div class="dialog-footer custom-footer">
              <div>
                <el-input v-model="callsign" style="max-width: 600px" placeholder="请输入">
                  <template #prepend>呼号：</template>
                </el-input>
              </div>
              <br>
              <div>
                <el-select v-model="moment" placeholder="选择预计放行时间" size="large" style="width: 100%">
                  <el-option v-for="item in momentoptions" :key="item.value" :label="item.label" :value="item.value"/>
                </el-select>
              </div>
              <br>
              <div>
                <el-select v-model="aircraft" placeholder="选择机型" size="large" style="width: 100%">
                  <el-option v-for="item in aircraftoptions" :key="item.value" :label="item.label" :value="item.value"/>
                </el-select>
              </div>
              <br>
              <div>
                <el-select v-model="park" placeholder="选择停机位" size="large" style="width: 100%">
                  <el-option v-for="item in parkoptions" :key="item.value" :label="item.label" :value="item.value"/>
                </el-select>
              </div>
              <br>
              <div>
                <el-select v-model="release" placeholder="选择放行方式" size="large" style="width: 100%">
                  <el-option v-for="item in releaseoptions" :key="item.value" :label="item.label" :value="item.value"/>
                </el-select>
              </div>
              <br>
              <el-button type="primary" style="width: 100%" @click="signupflight">提交</el-button>
            </div>
          </template>
        </el-dialog>
        <div :class="containerClass" :style="containerStyle()">
          <el-card :class="cardClass" class="title el-card">
            <h1 style="font-size: 45px;text-align: left">
              <el-header class="el-header" style="margin-bottom: 10px; font-size: 35px;">
                <el-icon class="icon-1">
                  <notebook/>
                </el-icon>
                活动报名
              </el-header>
            </h1>
          </el-card>
          <br>
          <el-card :class="cardClass" class="title el-card">
            <br>
            <el-tag size="large" :type="stateType">{{ state }}</el-tag>
            <h1 style="font-size: 25px;">起飞机场 / 落地机场</h1>
            <h1 style="font-size: 25px;">
              {{ eventsData.dep }} & {{ eventsData.dep_icao }} ✈ {{ eventsData.app }} & {{ eventsData.app_icao }}
            </h1>
            <h1>活动时间</h1>
            <h2>{{ receivedId }}</h2>
          </el-card>
          <br>
          <el-card class="display">
            <el-descriptions class="margin-top" title="详细信息" :column="3" border>
              <el-descriptions-item label="活动时间">
                {{ eventsData.time }}
              </el-descriptions-item>
              <el-descriptions-item label="起飞机场(DEP)">
                {{ eventsData.dep }} / {{ eventsData.dep_icao }}
              </el-descriptions-item>
              <el-descriptions-item label="落地机场(APP)">
                {{ eventsData.app }} / {{ eventsData.app_icao }}
              </el-descriptions-item>
              <el-descriptions-item label="建议导航数据">
                {{ eventsData.nav }}
              </el-descriptions-item>
              <el-descriptions-item label="活动航路">
                {{ eventsData.route }}
              </el-descriptions-item>
            </el-descriptions>
            <el-descriptions class="margin-top" :column="3" border>
              <el-descriptions-item label="活动开始时间">
                {{ eventsData.starttime }}
              </el-descriptions-item>
              <el-descriptions-item label="放行开始时间">
                {{ eventsData.starttime_re }}
              </el-descriptions-item>
              <el-descriptions-item label="放行结束时间">
                {{ eventsData.endtime_re }}
              </el-descriptions-item>
              <el-descriptions-item label="飞行方向">
                {{ flightdirection }}
              </el-descriptions-item>
              <el-descriptions-item label="备注">
                {{ eventsData.more ? eventsData.more : '无' }}
              </el-descriptions-item>
            </el-descriptions>
          </el-card>
          <br>
          <el-card class="display">
            <el-tabs v-model="activeName" class="demo-tabs" @tab-click="handleClick" tab-position="right">
              <el-tab-pane label="机组报名" name="first">
                <br>
                <div class="header-container">
                  <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
                    <el-icon class="icon-1">
                      <user/>
                    </el-icon>
                    机组报名
                  </el-header>
                  <el-button :type="pilot_login_sta" plain @click="clickHandler" style="margin-left: auto;"
                             :disabled="isDisabled_pilot">
                    {{ pilot_login_text }}
                  </el-button>
                </div>
                <br>
                <el-table :data="pilot" style="width: 100%"
                          :default-sort="{ prop: 'takeoff_time', order: 'ascending' }">
                  <el-table-column label="#" width="50">
                    <template #default="scope">
                      {{ scope.$index + 1 }}
                    </template>
                  </el-table-column>
                  <el-table-column prop="user" label="CID" width="190" >
                    <template v-slot="scope">
                      <el-tooltip
                          class="box-item"
                          effect="dark"
                          :content="'查看 ' + scope.row.user + ' 个人信息'"
                          placement="top"

                      >
                        <span   @click="watchuser(scope.row.user)">{{ scope.row.user }}</span>
                      </el-tooltip>
                    </template>
                  </el-table-column>
                  <el-table-column prop="callsign" label="呼号" width="180"/>
                  <el-table-column prop="aircraft" label="机型" width="180"/>
                  <el-table-column prop="takeoff_time" label="起飞时间" sortable/>
                  <el-table-column prop="park" label="停机位" width="180"/>
                  <el-table-column prop="manner" label="放行方式"/>
                </el-table>
              </el-tab-pane>
              <el-tab-pane label="管制报名" name="second">
                <br>
                <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
                  <el-icon class="icon-1">
                    <service/>
                  </el-icon>
                  管制报名
                </el-header>
                <el-row :gutter="20" style="text-align: center">
                  <el-col v-for="(item, index) in atcData" :key="index" :span="6">
                    <div class="grid-content ep-bg-purple"/>
                    <el-tooltip
                        class="box-item"
                        effect="dark"
                        :content="'点击报名 ' + item.atc"
                        placement="top"
                    >
                      <el-card @click="showAtcDialog(item)" class="custom-card-atc">
                        <transition name="custom-fade">
                          <div v-if="item.sta === 1 && item.user.id === user_num" class="custom-header"
                               style="background-color:#ebb563; border: 3px solid #E6A23C;">
                            <h1>您的席位</h1>
                          </div>
                          <div v-else-if="item.sta === 1" class="custom-header"
                               style="background-color: #f78989; border: 3px solid #F56C6C;">
                            <h1>席位激活</h1>
                          </div>
                          <div v-else class="custom-header"
                               style="background-color:#85ce61; border: 3px solid #67C23A;">
                            <h1>席位空闲</h1>
                          </div>
                        </transition>


                        <br>
                        <div>
                          <el-tag type="primary" size="large">席位: {{ item.atc }}</el-tag>
                        </div>
                        <br>
                        <div>
                          <el-tag type="primary">频率: {{ item.atc_fp }}</el-tag>
                        </div>
                        <br>
                        <div v-if="item.user && item.supervision">
                          <el-tag type="warning">
                            <el-tooltip
                                class="box-item"
                                effect="dark"
                                :content="'查看 ' + item.user.id  + ' 个人信息'"
                                placement="top"

                            >
                              <span   @click="watchuser(item.user.id )"> 管制人员: {{ item.user.id }}(学员)</span>
                            </el-tooltip>
                           </el-tag>
                        </div>
                        <div v-else-if="item.user">
                          <el-tag type="primary">
                            <el-tooltip
                                class="box-item"
                                effect="dark"
                                :content="'查看 ' + item.user.id  + ' 个人信息'"
                                placement="top"

                            >
                              <span   @click="watchuser(item.user.id )"> 管制人员: {{ item.user.id }}</span>
                            </el-tooltip></el-tag>
                        </div>
                        <br>
                        <div v-if="item.supervision && item.supervision.id !== 'NULL'">
                          <el-tag type="success">
                            <el-tooltip
                                class="box-item"
                                effect="dark"
                                :content="'查看 ' + item.supervision.id + ' 个人信息'"
                                placement="top"

                            >
                              <span   @click="watchuser(item.supervision.id )">   监管人员: {{ item.supervision.id }}</span>
                            </el-tooltip>
                          </el-tag>
                        </div>
                        <div v-else-if="item.supervision">
                          <el-tag type="warning">监管人员: 暂无监管</el-tag>
                        </div>
                        <br>
                      </el-card>
                    </el-tooltip>
                  </el-col>
                </el-row>
              </el-tab-pane>
              <el-tab-pane label="机场天气" name="third">
                <br>
                <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
                  <el-icon class="icon-1">
                    <wind-power/>
                  </el-icon>
                  {{ eventsData.dep_icao }} 天气 - - - (起飞机场)
                </el-header>
                <div class="weather">
                  <h1>METAR </h1>
                  <h3>{{ depweather.weather.metar }}</h3>
                  <br/>
                  <h2 style="color: #b25252">发布时间: {{ depweather.weather.metar_decode.time }}</h2>
                  <h2>
                    风况: {{ depweather.weather.metar_decode.wind_dir }}°{{
                      depweather.weather.metar_decode.wind_speed
                    }}{{ depweather.weather.metar_decode.wind_unit }}
                  </h2>
                  <h2>
                    能见度: {{
                      depweather.weather.metar_decode.visibility
                    }}{{ depweather.weather.metar_decode.visibility_unit }}
                  </h2>
                  <h2>修正海压: {{ depweather.weather.metar_decode.qnh }}{{
                      depweather.weather.metar_decode.qnh_unit
                    }}</h2>
                  <h2>温度: {{ depweather.weather.metar_decode.temperature }} °C</h2>
                  <h2>露点温度: {{ depweather.weather.metar_decode.dewpoint }} °C</h2>
                  <h2>预报: {{ depweather.weather.metar_decode.forcast }}</h2>
                  <br/>
                  <h1>TAF </h1>
                  <h3>{{ depweather.weather.taf }}</h3>
                </div>
                <br/>
                <hr/>
                <br/>
                <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
                  <el-icon class="icon-1">
                    <wind-power/>
                  </el-icon>
                  {{ eventsData.app_icao }} 天气 - - - (落地机场)
                </el-header>
                <div class="weather">
                  <h1>METAR </h1>
                  <h3>{{ appweather.weather.metar }}</h3>
                  <br/>
                  <h2 style="color: #b25252">发布时间: {{ appweather.weather.metar_decode.time }}</h2>
                  <h2>
                    风况: {{ appweather.weather.metar_decode.wind_dir }}°{{
                      appweather.weather.metar_decode.wind_speed
                    }}{{ appweather.weather.metar_decode.wind_unit }}
                  </h2>
                  <h2>
                    能见度: {{
                      appweather.weather.metar_decode.visibility
                    }}{{ appweather.weather.metar_decode.visibility_unit }}
                  </h2>
                  <h2>修正海亚: {{ appweather.weather.metar_decode.qnh }}{{
                      appweather.weather.metar_decode.qnh_unit
                    }}</h2>
                  <h2>温度: {{ appweather.weather.metar_decode.temperature }} °C</h2>
                  <h2>露点温度: {{ appweather.weather.metar_decode.dewpoint }} °C</h2>
                  <h2>预报: {{ appweather.weather.metar_decode.forcast }}</h2>
                  <br/>
                  <h1>TAF </h1>
                  <h3>{{ appweather.weather.taf }}</h3>
                </div>
              </el-tab-pane>
            </el-tabs>
          </el-card>
          <br><br><br>
        </div>
      </div>
    </transition>

  </div>
</template>


<script>
import {defineComponent, watch} from 'vue';
import {useRoute} from 'vue-router';
import axios from 'axios';
import {isDark} from '../composables/dark';
import {AlarmClock, Notebook, Position, Service, UserFilled, WindPower} from '@element-plus/icons-vue';
import loading from "./loading.vue";

export default defineComponent({
  components: {Notebook, Position, AlarmClock, Service, UserFilled, WindPower},
  data() {
    return {
      load: true,
      main: false,
      user_num: sessionStorage.getItem("user_num"),
      user_grade: sessionStorage.getItem("user_grade"),
      receivedId: null,
      callsign: null,
      atc_view: false,
      selectedAtc: [],
      atc_wait_list: [],
      atc_wait_list_sta: null,
      pilot_login: false,
      pilot_login_sta: 'warning',
      pilot_login_text: '加载中。。。',
      isDisabled_pilot: true,
      useAlternativeHandler: false,
      bgimage:null,
      release: '请选择放行方式',
      releaseoptions: [
        {label: '语音放行(CN)', value: '1'},
        {label: '语音放行(EN)', value: '2'},
        {label: 'PDC', value: '3'}
        // 可以继续添加更多的选项
      ],
      aircraft: '请选择机型',
      aircraftoptions:
          [{label: '', value: ''}],
      moment: '请选择预计放行时间',
      momentoptions:
          [{label: '', value: ''}],
      park: '请先选择机型',
      parkoptions:
          [{label: '', value: ''}],
      cardClass:
          'title',
      containerClass:
          'container',
      activeName:
          'first',
      eventsData:
          {
            dep: '',
            dep_icao:
                '',
            app:
                '',
            app_icao:
                '',
            time:
                '',
            nav:
                '',
            route:
                '',
            starttime:
                '',
            starttime_re:
                '',
            more:
                '',
            state:
                '',
            direction:
                '',
            endtime_re:
                ''
          }
      ,
      atcData: [],
      flightdirection: '',
      stateType:
          '',
      state:
          '',
      depweather:
          {
            weather: {
              metar: '',
              taf:
                  '',
              metar_decode:
                  {
                    time: '',
                    wind_dir:
                        '',
                    wind_speed:
                        '',
                    wind_unit:
                        '',
                    qnh:
                        '',
                    qnh_unit:
                        '',
                    visibility:
                        '',
                    visibility_unit:
                        '',
                    temperature:
                        '',
                    dewpoint:
                        '',
                    forcast:
                        ''
                  }
            }
          }
      ,
      appweather: {
        weather: {
          metar: '',
          taf:
              '',
          metar_decode:
              {
                time: '',
                wind_dir:
                    '',
                wind_speed:
                    '',
                wind_unit:
                    '',
                qnh:
                    '',
                qnh_unit:
                    '',
                visibility:
                    '',
                visibility_unit:
                    '',
                temperature:
                    '',
                dewpoint:
                    '',
                forcast:
                    ''
              }
        }
      }
      ,
      pilot: []

    }
        ;
  },
  watch: {
    aircraft(newValue) {
      this.park = '请选择停机位';
      this.fetchAircraftData(newValue);
    },
    'eventsData.time': function (newTime) {
      this.geteventdata(newTime);
    }
  },
  created() {
    watch(
        isDark,
        (newValue) => {
          this.cardClass = newValue ? 'title-dark' : 'title';
          this.containerClass = newValue ? 'container-dark' : 'container';
        },
        {immediate: true}
    );
  },
  mounted() {
    const route = useRoute();
    const id = route.params.id;
    if (id) {
      this.receivedId = id;
      this.bgimage=this.containerStyle();
      console.log(this.bgimage);
      this.Getmomentlist()
      this.geteventdata(id);
      this.getparkdata(this.eventsData.dep_icao);
    } else {
      console.error('Invalid id:', id);
    }
  },
  methods: {
    watchuser(id){
      this.$router.push({name: 'users', params: {id: id}});
    },
    handlePilotLogin() {
      this.pilot_login = true;

      // 根据需要，添加更多逻辑
    },
    handleAlternativeLogin() {
      const id = this.receivedId;
      const data = {
        token: 'ab321818',
        id: id,
        userid: sessionStorage.getItem("user_num"),
      };

      // Send data using POST request
      fetch('https://server.skydreamclub.cn/CancelFlightByID.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
          .then(response => response.json())
          .then(data => {

            this.$nextTick(() => {
              this.pilot_login = false;
              this.showSuccessNotification("取消成功");
              this.useAlternativeHandler=this.handlePilotLogin;
              // window.reload();
              this.geteventdata(this.eventsData.time);
              this.checkflightsta(this.eventsData.time);


            });
          })

          .catch((error) => {
            console.error('Error:', error.message);
            this.showErrorNotification("取消失败");
          });

      this.forceUpdate();
    },
    async geteventdata(id) {

      try {
        const response = await axios.get(`https://server.skydreamclub.cn/GetEventDataByID.php?id=${id}`);

        this.eventsData = [];
        const data = response.data.data;
        if (data.length <= 0) {
          this.$router.push({name: 'error'});
        }
        this.eventsData = data.basic[0];
        this.depweather = data.weather.dep;
        this.appweather = data.weather.app;
        this.pilot = data.user.pilot;
        this.atcData = data.user.atc;
        switch (this.eventsData.direction) {
          case '1':
            this.flightdirection = '向西飞行请选择双数高度层: 9200米(FL301)、9800米(FL321)、10400米(FL341)、11000米(FL361)、11600米(FL381)、12200米(FL401)';
            break;
          case '2':
            this.flightdirection = '向东飞行请使用单数高度层: 8900米(FL291)、9500米(FL311)、10100米(FL331)、10700米(FL351)、11300米(FL371)、11900米(FL391)、12500米(FL411)';
            break;
          default:
            this.flightdirection = '管理员未填写';
            break;
        }

        switch (this.eventsData.state) {
          case '1':
            this.state = '报名中';
            this.stateType = 'success';
            break;
          case '2':
            this.state = '正在进行';
            this.stateType = 'primary';
            break;
          case '3':
            this.state = '已结束';
            this.stateType = 'warning';
            break;
          case '4':
            this.state = '已取消';
            this.stateType = 'danger';
            break;
        }
        this.checkflightsta(id);
      } catch (error) {
        console.error('Error fetching event data:', error);
      }
      this.load = false;
      this.main = true;
    },
    async getparkdata(icao) {
      try {
        const response = await axios.get(`https://server.skydreamclub.cn/GetAirportGradebyICAO.php?icao=${this.eventsData.dep_icao}`);


        const data = response.data.dara;

        this.aircraftoptions = data.map(item => ({
          label: item,
          value: item
        }));


      } catch (error) {
        console.error('Error fetching event data:', error);
      }
    },
    async fetchAircraftData(aircraft) {
      try {
        const response = await axios.get(`https://server.skydreamclub.cn/GetParksByICAOAndAircraft.php?icao=${this.eventsData.dep_icao}&time=${this.receivedId}&aircraft=${aircraft}`);


        const data = response.data.data;

        this.parkoptions = data.map(item => ({
          label: item,
          value: item
        }));


      } catch (error) {
        console.error('Error fetching event data:', error);
      }
    },
    async Getmomentlist() {
      try {
        const response = await axios.get(`https://server.skydreamclub.cn/GetMomentList.php?id=${this.receivedId}`);

        const data = response.data;

        this.momentoptions = data.map(item => ({
          label: item,
          value: item
        }));


      } catch (error) {
        console.error('Error fetching event data:', error);
      }
    },
    async checkflightsta(id) {
      try {
        const requestData = {
          token: 'ab321818',
          id: id,
          userid: sessionStorage.getItem("user_num")
        };

        const response = await axios.post(`https://server.skydreamclub.cn/CheckUserFlightByID.php`, requestData);


        const data = response.data;
        if (data.status === '200' && this.eventsData.state === '1') {
          this.pilot_login_text = ' + 报名';
          this.pilot_login_sta = 'success';
          this.isDisabled_pilot = false;
          this.useAlternativeHandler=false;
        } else if (this.eventsData.state === '2') {
          this.pilot_login_text = ' X 正在进行';
          this.pilot_login_sta = 'primary';
          this.isDisabled_pilot = true;
        } else if (this.eventsData.state === '3') {
          this.pilot_login_text = ' X 已结束';
          this.pilot_login_sta = 'warning';
          this.isDisabled_pilot = true;
        } else if (this.eventsData.state === '4') {
          this.pilot_login_text = ' X 已取消';
          this.pilot_login_sta = 'danger';
          this.isDisabled_pilot = true;
        } else {
          this.pilot_login_text = ' 取消报名 ';
          this.pilot_login_sta = 'danger';
          this.useAlternativeHandler = true;
          this.isDisabled_pilot = false;
        }
      } catch (error) {
        console.error('Error fetching event data:', error);
      }

    },
    signupflight() {


      // Check if all required fields are filled
      if (this.callsign !== null &&
          this.moment !== '请选择预计放行时间' &&
          this.aircraft !== null &&
          this.park !== '请先选择机型' &&
          this.park !== '请选择停机位' &&
          this.release !== '请选择放行方式') {

        const id = this.receivedId;
        // Prepare data to send
        const data = {
          token: 'ab321818',
          id: id,
          userid: sessionStorage.getItem("user_num"),
          callsign: this.callsign,
          moment: this.moment,
          aircraft: this.aircraft,
          park: this.park,
          manner: this.release
        };

        // Send data using POST request
        fetch('https://server.skydreamclub.cn/SignUpFlightByID.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {

              this.$nextTick(() => {
                this.pilot_login = false;
                this.showSuccessNotification("报名成功");
                // window.reload();
                this.geteventdata(this.eventsData.time);
                this.checkflightsta(this.eventsData.time);
                // this.forceUpdate();
              });
            })

            .catch((error) => {
              console.error('Error:', error.message);
              this.showErrorNotification("报名失败");
            });

      } else {
        this.showErrorNotification('请填写所有信息，谢谢！');
      }
      this.forceUpdate();
    },
    forceUpdate() {
      this.$nextTick(() => {
        this.$forceUpdate();
      });
    },
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

    showAtcDialog(item) {
      this.atc_wait_list = [];
      this.selectedAtc = item; // Set the selectedAtc data to the item clicked
      this.atc_view = true; // Show the dialog by setting its v-model to true
      if (item.sta === 1) {
        this.getwaitlist(this.receivedId, item.atc);
        this.getatcwaitliststa(item.atc);
      }
    },
    async getwaitlist(time, atc) {
      fetch(`https://server.skydreamclub.cn/GetAtcWaitListByTime.php?time=${time}&atc=${atc}`, {
        method: 'GET',
      })
          .then(response => response.json())
          .then(data => {

            this.atc_wait_list = data.data;
          })

          .catch((error) => {
            console.error('Error:', error.message);
            this.showErrorNotification("获取失败");
          });

    },
    getatcwaitliststa(dataIdValue) {
      const data = {
        token: 'ab321818',
        time: this.receivedId,
        cid: sessionStorage.getItem("user_num"),
        atc: dataIdValue
      };

      // Send data using POST request
      fetch('https://server.skydreamclub.cn/CheckUserAtcListByID.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
          .then(response => response.json())
          .then(data => {

            this.atc_wait_list_sta = data.status;
          })

          .catch((error) => {
            console.error('Error:', error.message);
            this.showErrorNotification("获取状态失败");
          });

      this.forceUpdate();
    },
    cancelatcwaitlist(dataIdValue) {
      const data = {
        token: 'ab321818',
        time: this.receivedId,
        cid: sessionStorage.getItem("user_num"),
        atc: dataIdValue
      };

      // Send data using POST request
      fetch('https://server.skydreamclub.cn/CancelAtcWaitListByID.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
          .then(response => response.json())
          .then(data => {
            this.atc_view = false;
            this.showSuccessNotification("候补取消成功");
            this.atc_wait_list = [];
          })

          .catch((error) => {
            console.error('Error:', error.message);
            this.showErrorNotification("获取状态失败");
          });

      this.forceUpdate();
    },

    cancelatc(dataIdValue) {
      const id = this.receivedId;
      const data = {
        token: 'ab321818',
        time: this.receivedId,
        atc_user: sessionStorage.getItem("user_num"),
        atc: dataIdValue
      };

      // Send data using POST request
      fetch('https://server.skydreamclub.cn/CancelAtcByID.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
          .then(response => response.json())
          .then(data => {
            console.log(data);
            this.$nextTick(() => {
              this.atc_view = false;
              this.showSuccessNotification("取消成功");
              this.geteventdata(this.eventsData.time);
              this.checkflightsta(this.eventsData.time);
            });
          })

          .catch((error) => {
            console.error('Error:', error.message);
            this.showErrorNotification("取消失败");
          });

      this.forceUpdate();
    },
    signupatc(dataIdValue) {
      const data = {
        token: 'ab321818',
        time: this.receivedId,
        atc_user: sessionStorage.getItem("user_num"),
        atc: dataIdValue
      };

      // Send data using POST request
      fetch('https://server.skydreamclub.cn/SignUpAtcByID.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
          .then(response => response.json())
          .then(data => {

            this.$nextTick(() => {
              this.atc_view = false;
              this.showSuccessNotification("报名成功");
              this.geteventdata(this.eventsData.time);
              this.checkflightsta(this.eventsData.time);

            });
          })

          .catch((error) => {
            console.error('Error:', error.message);
            this.showErrorNotification("报名失败");
          });

      this.forceUpdate();
    },
    signupsupatc(dataIdValue) {
      const data = {
        token: 'ab321818',
        time: this.receivedId,
        atc: dataIdValue,
        supervision: sessionStorage.getItem("user_num")
      };

      // Send data using POST request
      fetch('https://server.skydreamclub.cn/SignUpAtcSupervisionByID.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
          .then(response => response.json())
          .then(data => {

            this.$nextTick(() => {
              this.atc_view = false;
              this.showSuccessNotification("报名成功");
              this.geteventdata(this.eventsData.time);
              this.checkflightsta(this.eventsData.time);
            });
          })

          .catch((error) => {
            console.error('Error:', error.message);
            this.showErrorNotification("报名失败");
          });

      this.forceUpdate();
    },
    signupatcwaitlist(dataIdValue) {
      const data = {
        token: 'ab321818',
        time: this.receivedId,
        cid: sessionStorage.getItem("user_num"),
        atc: dataIdValue
      };

      // Send data using POST request
      fetch('https://server.skydreamclub.cn/SignAtcWaitList.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
          .then(response => response.json())
          .then(data => {

            this.$nextTick(() => {
              this.atc_view = false;
              this.showSuccessNotification("候补报名成功");
              this.geteventdata(this.eventsData.time);
              this.checkflightsta(this.eventsData.time);
            });
          })

          .catch((error) => {
            console.error('Error:', error.message);
            this.showErrorNotification("候补报名失败");
          });

      this.forceUpdate();
    },

    cancelatcsup(dataIdValue) {
      const id = this.receivedId;
      const data = {
        token: 'ab321818',
        time: this.receivedId,
        atc: dataIdValue,
        supervision: sessionStorage.getItem("user_num")
      };

      // Send data using POST request
      fetch('https://server.skydreamclub.cn/CancelAtcSupervisionByID.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
          .then(response => response.json())
          .then(data => {

            this.$nextTick(() => {
              this.atc_view = false;
              this.showSuccessNotification("取消成功");
              this.geteventdata(this.eventsData.time);
              this.checkflightsta(this.eventsData.time);
            });
          })

          .catch((error) => {
            console.error('Error:', error.message);
            this.showErrorNotification("取消失败");
          });

      this.forceUpdate();
    },
    containerStyle() {
      return {
        backgroundImage: `url('https://server.skydreamclub.cn/posters/${this.receivedId}.jpeg')`,
      };
    }

  },

  computed: {
    loading() {
      return loading
    },
    clickHandler() {
      return this.useAlternativeHandler ? this.handleAlternativeLogin : this.handlePilotLogin;
    },

  }
});
</script>

<style scoped>

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

.container,
.container-dark {
  border-radius: 20px;

  background-size: cover;
  background-position: center;
  position: relative;
  width: 95.3%;
  height: 100vh;
  padding: 30px;
  z-index: 0;
}

.el-card {
  z-index: 1;
  border-radius: 20px;
}

.el-card:hover {
  transform: translateY(-10px);
  transition: transform 0.5s ease;
}


.title {
  text-align: center;
  border-radius: 20px;
  background-color: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
}

.title-dark {
  text-align: center;
  border-radius: 20px;
  background-color: rgba(0, 0, 0, 0.5);
}

.title:hover {
  transform: translateY(-10px);
  transition: transform 0.5s ease;
}

.container::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to top, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0));
  border-radius: 20px;
  z-index: -1;
}

.container-dark::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to top, rgba(0, 0, 0, 1), rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0));
  border-radius: 20px;
  z-index: -1;
}

.title {
  color: var(--title-color);
}

.custom-dialog {
  border-radius: 20px;
}

.custom-footer {
  border-bottom-left-radius: 20px;
  border-bottom-right-radius: 20px;
}

.header-container {
  display: flex;
  align-items: center;
}

.el-fade-in-linear-enter-active, .el-fade-in-linear-leave-active {
  transition: opacity 0.5s linear;
}

.el-fade-in-linear-enter, .el-fade-in-linear-leave-to {
  opacity: 0;
}

.el-row {
  margin-bottom: 20px;
}

.el-row:last-child {
  margin-bottom: 0;
}

.el-col {
  border-radius: 4px;
}

.grid-content {
  border-radius: 4px;
  min-height: 36px;
}

.custom-card {
  position: relative;
  overflow: hidden;
}

.custom-header {
  color: #f9f9f9;
  padding: 10px;
  border-radius: 20px;
}

.custom-header el-tag {
  font-size: 20px; /* Custom font size for the tag */
  font-weight: bold; /* Bold text */
}

.custom-fade-enter-active, .custom-fade-leave-active {
  animation: fade-in 0.8s ease-out forwards, immediate-hide 0s ease-out;
}

.custom-fade-leave-active {
  animation: immediate-hide 0s ease-out;
}

@keyframes fade-in {
  0% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
}

@keyframes immediate-hide {
  0% {
    opacity: 1;
  }
  100% {
    opacity: 0;
  }
}

</style>
