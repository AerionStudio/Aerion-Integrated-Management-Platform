<script>

import {defineComponent} from "vue";
import {Plus} from "@element-plus/icons-vue";

export default defineComponent({
  components: {Plus},
  data() {
    return {
      qq: 'https://server.skydreamclub.cn/qq.png',
      kook: 'https://server.skydreamclub.cn/kook.ico',
      twr: false,
      app: false,
      ctr: false,
      twr_en: null,
      twr_experience: null,
      twr_online_time: null,
      twr_connect: null,
      twr_reason: null,
      app_en: null,
      app_experience: null,
      app_online_time: null,
      app_connect: null,
      app_reason: null,
      ctr_en: null,
      ctr_experience: null,
      ctr_online_time: null,
      ctr_connect: null,
      ctr_reason: null,
      user_num: sessionStorage.getItem("user_num")
    }
  },
  methods: {
    joinqq() {
      window.location.href = "https://qm.qq.com/cgi-bin/qm/qr?_wv=1027&k=eJkEkHYXzXUe4F5dG2Wc2FpoLcH1kCEd&authKey=7EWYnz90DKNohJMzDPtNHPo%2B4fyi%2FwaZSaBiFSXsDWIjEnBDvkuSmQn6kKmlkkKG&noverify=0&group_code=700540227";
    },
    joinkook() {
      window.location.href = "https://www.kookapp.cn/app/invite/jUb5Ei";
    },
    jointwr() {
      const en = this.twr_en;
      const experience = this.twr_experience;
      const onlinetime = this.twr_online_time;
      const connect = this.twr_connect;
      const reason = this.twr_reason;

      if (en != null && experience != null && onlinetime != null && connect != null && reason != null) {
        console.log(reason);
        const data = {
          token: 'ab321818',
          cid: this.user_num,
          atcname: '塔台管制室',
          en: en,
          experience: experience,
          time: onlinetime,
          connect: connect,
          reason: reason
        };

        // Send data using POST request
        fetch('https://server.skydreamclub.cn/SendAtc.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
              console.log(data);
              if (data.status == '200') {
                this.showSuccessNotification("提交成功，将于三个工作日内给予答复！");
                this.twr = false;
              } else {
                this.showErrorNotification('提交失败，请重试');
              }

            })

            .catch((error) => {
              this.showErrorNotification('提交失败，请重试');
            });
      } else {
        this.showErrorNotification("请输入所有数据");
      }

    },
    joinctr() {
      const en = this.ctr_en;
      const experience = this.ctr_experience;
      const onlinetime = this.ctr_online_time;
      const connect = this.ctr_connect;
      const reason = this.ctr_reason;
      console.log(reason);
      if (en != null&& experience != null && onlinetime != null && connect != null && reason != null) {
        const data = {
          token: 'ab321818',
          cid: this.user_num,
          atcname: '区域管制室',
          en: en,
          experience: experience,
          time: onlinetime,
          connect: connect,
          reason: reason
        };

        // Send data using POST request
        fetch('https://server.skydreamclub.cn/SendAtc.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
              console.log(data);
              if (data.status == '200') {
                this.showSuccessNotification("提交成功，将于三个工作日内给予答复！");
                this.ctr = false;
              } else {
                this.showErrorNotification('提交失败，请重试');
              }

            })

            .catch((error) => {
              this.showErrorNotification('提交失败，请重试');
            });
      } else {
        this.showErrorNotification("请输入所有数据");
      }


    },
    joinapp() {
      const en = this.app_en;
      const experience = this.app_experience;
      const onlinetime = this.app_online_time;
      const connect = this.app_connect;
      const reason = this.app_reason;
      console.log(reason);
      if (en != null&& experience != null && onlinetime != null && connect != null && reason != null) {

        const data = {
          token: 'ab321818',
          cid: this.user_num,
          atcname: '进近管制室',
          en: en,
          experience: experience,
          time: onlinetime,
          connect: connect,
          reason: reason
        };

        // Send data using POST request
        fetch('https://server.skydreamclub.cn/SendAtc.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
              console.log(data);
              if (data.status == '200') {
                this.showSuccessNotification("提交成功，将于三个工作日内给予答复！");
                this.app = false;
              } else {
                this.showErrorNotification('提交失败，请重试');
              }

            })

            .catch((error) => {
              this.showErrorNotification('提交失败，请重试');
            });

      }else {
        this.showErrorNotification("请输入所有数据");
      }

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
  }
})
</script>

<template><br>
  <el-dialog v-model="twr" title="加入塔台管制室" width="800">
    <div style="text-align: center">
      <h1 style="font-size: 30px">呼号: {{ user_num }}</h1>
      <h1 style="font-size: 30px">申请管制室: 塔台管制室</h1>
      <el-form ref="twrForm" label-width="120px" class="login-form">
        <el-form-item label="英语水平" prop="username">
          <el-input v-model="twr_en" placeholder="请输入英语水平"></el-input>
        </el-form-item>
        <el-form-item label="是否有管制经验" prop="password">
          <el-input v-model="twr_experience" placeholder="请输入是 / 否"></el-input>
        </el-form-item>
        <el-form-item label="在线时间" prop="password">
          <el-input v-model="twr_online_time" placeholder="请输入在线时间"></el-input>
        </el-form-item>
        <el-form-item label="联系方式" prop="password">
          <el-input v-model="twr_connect" placeholder="请输入联系方式"></el-input>
        </el-form-item>
        <el-form-item label="申请理由" prop="password">
          <el-input v-model="twr_reason" style="height: 120px" placeholder="请输入申请理由"></el-input>
        </el-form-item>
        <br> <br>
        <el-form-item>
          <el-button type="primary" style="width: 100%" @click="jointwr">提交</el-button>
        </el-form-item>
      </el-form>
    </div>
  </el-dialog>
  <el-dialog v-model="app" title="加入进近管制室" width="800">
    <div style="text-align: center">
      <h1 style="font-size: 30px">呼号: {{ user_num }}</h1>
      <h1 style="font-size: 30px">申请管制室: 进近管制室</h1>
      <el-form ref="loginForm" label-width="120px" class="login-form">
        <el-form-item label="英语水平" prop="username">
          <el-input v-model="app_en" placeholder="请输入英语水平"></el-input>
        </el-form-item>
        <el-form-item label="是否有管制经验" prop="password">
          <el-input v-model="app_experience" placeholder="请输入是 / 否"></el-input>
        </el-form-item>
        <el-form-item label="在线时间" prop="password">
          <el-input v-model="app_online_time" placeholder="请输入在线时间"></el-input>
        </el-form-item>
        <el-form-item label="联系方式" prop="password">
          <el-input v-model="app_connect" placeholder="请输入联系方式"></el-input>
        </el-form-item>
        <el-form-item label="申请理由" prop="password">
          <el-input v-model="app_reason" style="height: 120px" placeholder="请输入申请理由"></el-input>
        </el-form-item>
        <br> <br>
        <el-form-item>
          <el-button type="primary" style="width: 100%" @click="joinapp">提交</el-button>
        </el-form-item>
      </el-form>
    </div>
  </el-dialog>
  <el-dialog v-model="ctr" title="加入区域管制室" width="800">
    <div style="text-align: center">
      <h1 style="font-size: 30px">呼号: {{ user_num }}</h1>
      <h1 style="font-size: 30px">申请管制室: 区域管制室</h1>
      <el-form ref="loginForm" label-width="120px" class="login-form">
        <el-form-item label="英语水平" prop="username">
          <el-input v-model="ctr_en" placeholder="请输入英语水平"></el-input>
        </el-form-item>
        <el-form-item label="是否有管制经验" prop="password">
          <el-input v-model="ctr_experience" placeholder="请输入是 / 否"></el-input>
        </el-form-item>
        <el-form-item label="在线时间" prop="password">
          <el-input v-model="ctr_online_time" placeholder="请输入在线时间"></el-input>
        </el-form-item>
        <el-form-item label="联系方式" prop="password">
          <el-input v-model="ctr_connect" placeholder="请输入联系方式"></el-input>
        </el-form-item>
        <el-form-item label="申请理由" prop="password">
          <el-input v-model="ctr_reason" style="height: 120px" placeholder="请输入申请理由"></el-input>
        </el-form-item>
        <br> <br>
        <el-form-item>
          <el-button type="primary" style="width: 100%" @click="joinctr">提交</el-button>
        </el-form-item>
      </el-form>
    </div>
  </el-dialog>
  <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
    <el-icon class="icon-1">
      <plus/>
    </el-icon>
    加入SkyDreamClub平台
  </el-header>
  <h1 style="font-size: 30px;padding: 20px">
    SkyDreamClub是一个为广大模拟飞行玩家提供的集交流学习、联线飞行等服务于一体的在线平台,快来加入我们吧！
  </h1>
  <br>
  <el-row :gutter="20">
    <el-col :span="12">
      <div class="grid-content ep-bg-purple"/>
      <el-card @click="joinqq()" style="cursor: pointer;">
        <div class="content">
          <el-avatar :size="100" :src=qq
                     class="avatar"/>
          <div class="text">
            <h1 style="font-size: 30px">加入平台QQ群 | 700540227 </h1>
          </div>
        </div>
      </el-card>
    </el-col>
    <el-col :span="12">
      <div class="grid-content ep-bg-purple"/>
      <el-card @click="joinkook()" style="cursor: pointer;">
        <div class="content">
          <el-avatar :size="100" :src=kook
                     class="avatar"/>
          <div class="text">
            <h1 style="font-size: 30px">加入平台KOOK语音频道</h1>
          </div>
        </div>
      </el-card>
    </el-col>
  </el-row>
  <br><br><br>
  <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
    <el-icon class="icon-1">
      <plus/>
    </el-icon>
    加入SkyDreamClub管制室
  </el-header>
  <h1 style="font-size: 30px;padding: 20px">
    SkyDreamClub管制室秉承娱乐和专业的理念，致力于为玩家在工作学习之余提供一个高兴快乐的连飞环境。以机组高兴，管制愉悦，其乐融融为目标，建立一个团结，互助，灵活的平台环境。快来加入我们吧！
  </h1>
  <br>
  <el-row :gutter="20">
    <el-col :span="12">
      <div class="grid-content ep-bg-purple"/>
      <el-card @click="twr=true" style="cursor: pointer;">
        <h1 style="text-align: center;font-size: 35px">申请加入塔台管制室</h1>
      </el-card>
    </el-col>
    <el-col :span="12">
      <div class="grid-content ep-bg-purple"/>
      <el-card @click="app=true" style="cursor: pointer;">
        <h1 style="text-align: center;font-size: 35px">申请加入进近管制室</h1>
      </el-card>
    </el-col>
  </el-row>
  <br>
  <el-row :gutter="20">
    <el-col :span="12">
      <div class="grid-content ep-bg-purple"/>
      <el-card @click="ctr=true" style="cursor: pointer;">
        <h1 style="text-align: center;font-size: 35px">申请加入区域管制室</h1>
      </el-card>
    </el-col>

  </el-row>
  <br><br><br>

</template>

<style>
.content {
  display: flex;
  align-items: center;
  width: 100%;
}

.text {
  margin-left: 100px; /* Optional: Adds space between avatar and text */
}

.el-card:hover {
  transform: translateY(-10px);
  transition: transform 0.5s ease;
}

</style>