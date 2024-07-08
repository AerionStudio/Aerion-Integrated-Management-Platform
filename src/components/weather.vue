<script>


export default {
  data() {
    return {
      icao: null,
      weatherdata: null,
      load: false,
      display:false
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
      this.weatherdata=[];
      this.load=true;
      this.display=false;
      const icao = this.icao;
      const data = {
        token: 'ab321818',
        icao: icao,
      };

      // Send data using POST request
      fetch('https://server.skydreamclub.cn/GetWeather.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
          .then(response => response.json())
          .then(data => {

            if (data.airport != 'airports not found') {
              this.weatherdata = data;
              this.load=false;
              this.display=true;

              this.showSuccessNotification("查询成功！");
            } else {
              this.load=false;
              this.showErrorNotification('查询失败，请重试。');
            }
          })
          .catch((error) => {
            console.error('Error:', error.message);
            this.load=false;
            this.showErrorNotification('查询失败，请重试。');
          });
    }
  }
};
</script>

<template>

  <div class="main">
    <transition name="el-fade-in-linear">
      <el-card ><br>
        <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
          <el-icon class="icon-1">
            <wind-power/>
          </el-icon>
          天气查询
        </el-header>
        <br>
        <el-form-item label="机场ICAO" prop="icon">
          <el-input v-model="icao" placeholder="请输入机场ICAO"></el-input>
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
    <div v-if="display"> <!-- Use v-if instead of v-show for better control -->
      <transition name="el-fade-in-linear">
        <el-card>
          <div class="weather"><br>
            <el-header class="el-header" style="margin-bottom: 10px; font-size: 30px;">
              <el-icon class="icon-1">
                <Position/>
              </el-icon>
              {{ weatherdata.airport.gps_code }} & {{ weatherdata.airport.iata_code }} /
              {{ weatherdata.airport.cn_name }} # {{weatherdata.airport.iso_country}}
            </el-header>
            <br>
            <h1>METAR </h1>
            <h3>{{ weatherdata.weather.metar }}</h3>
            <br/>
            <h2 style="color: #b25252">发布时间: {{ weatherdata.weather.metar_decode.time }}</h2>
            <h2>
              风况: {{ weatherdata.weather.metar_decode.wind_dir }}°{{
                weatherdata.weather.metar_decode.wind_speed
              }}{{ weatherdata.weather.metar_decode.wind_unit }}
            </h2>
            <h2>
              能见度: {{
                weatherdata.weather.metar_decode.visibility
              }}{{ weatherdata.weather.metar_decode.visibility_unit }}
            </h2>
            <h2>修正海压: {{ weatherdata.weather.metar_decode.qnh }}{{
                weatherdata.weather.metar_decode.qnh_unit
              }}</h2>
            <h2>温度: {{ weatherdata.weather.metar_decode.temperature }} °C</h2>
            <h2>露点温度: {{ weatherdata.weather.metar_decode.dewpoint }} °C</h2>
            <h2>预报: {{ weatherdata.weather.metar_decode.forcast }}</h2>
            <br/>
            <h1>TAF </h1>
            <h3>{{ weatherdata.weather.taf }}</h3>
          </div>
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

.el-card:hover{
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
