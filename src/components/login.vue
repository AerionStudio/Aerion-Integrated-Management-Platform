<template>
  <div class="login-container" style="display: flex;">
    <el-card class="main">

      <h1 class="logo-login">
        <img src="../assets/logo.png" alt="" style="width: 20%;height:20%" class="logo-main">
        <h1 class="logo-text" style="font-size: 31px">SKYDREAM CLUB</h1>
      </h1>
      <br><br>
      <el-tabs tab-position="right" style="height: 430px" class="demo-tabs">
        <el-tab-pane label="登录">
          <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
            <el-icon class="icon-1">
              <user/>
            </el-icon>
            登录
          </el-header>
          <br><br>
          <el-form ref="loginForm" :model="loginForm" label-width="60px" class="login-form">
            <el-form-item label="呼号" prop="username">
              <el-input v-model="loginForm.username" placeholder="请输入呼号"></el-input>
            </el-form-item>
            <el-form-item label="密码" prop="password">
              <el-input type="password" v-model="loginForm.password" placeholder="请输入密码"></el-input>
            </el-form-item>
            <br> <br>
            <el-form-item>
              <el-button type="primary" @click="login" style="width: 100%">登录</el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>
        <el-tab-pane label="注册">
          <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
            <el-icon class="icon-1">
              <user/>
            </el-icon>
            注册
          </el-header>
          <el-form ref="loginForm" :model="RegForm" label-width="60px" class="login-form">
            <el-form-item label="呼号" prop="username">
              <el-input v-model="RegForm.usernum" @input="checkUsernum" placeholder="请输入呼号"></el-input>
            </el-form-item>
            <el-form-item label="密码" prop="password">
              <el-input type="password" v-model="RegForm.password" placeholder="请输入密码"></el-input>
            </el-form-item>

            <el-form-item label="QQ号" prop="password">
              <el-input type="text" @input="checkQQnum" v-model="RegForm.qq" placeholder="请输入QQ号"></el-input>
            </el-form-item>
            <div>
              <el-form-item label="邮箱" prop="password">
                <el-input type="text" v-model="RegForm.email" @input="checkemailnum"
                          placeholder="请输入邮箱"></el-input>
                <el-button size="small" :type="RegForm.capnumtype" style="margin-top: 10px;" @click="sendcapnum"
                           :disabled="RegForm.capnumsta">{{ RegForm.capnumtext }}
                </el-button>
              </el-form-item>
            </div>

            <div>
              <el-form-item label="验证码" prop="password">
                <el-input type="text" v-model="RegForm.capnum" placeholder="请输入验证码"></el-input>
              </el-form-item>
            </div>
            <el-form-item>
              <el-button type="primary" @click="reg" style="width: 100%">注册</el-button>
            </el-form-item>
          </el-form>
          <br><br></el-tab-pane>
        <el-tab-pane label="找回密码">
          <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
            <el-icon class="icon-1">
              <user/>
            </el-icon>
            找回密码
          </el-header>
          <el-form ref="loginForm" :model="ForForm" label-width="60px" class="login-form">

            <el-form-item label="邮箱" prop="password">
              <el-input type="text" v-model="ForForm.email"
                        placeholder="请输入邮箱"></el-input>
              <el-button size="small" :type="ForForm.capnumtype" style="margin-top: 10px;" @click="sendcapnumfor"
                         :disabled="ForForm.capnumsta">{{ ForForm.capnumtext }}
              </el-button>
            </el-form-item>

            <el-form-item label="验证码" prop="password">
              <el-input type="text" v-model="ForForm.capnum" placeholder="请输入验证码"></el-input>
            </el-form-item>
            <el-form-item label="新密码" prop="username">
              <el-input v-model="ForForm.pwd" type="password" placeholder="请输入新密码"></el-input>
            </el-form-item>
            <br> <br>
            <el-form-item>
              <el-button type="primary" @click="forget" style="width: 100%">重置密码</el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>
      </el-tabs>
      <p @click="joinicp()">湘ICP备2024046403号-5</p>
      <p style="margin-top: -15px">AIMP V1.0 Copyright © 2024 AerionStudio All right reserved.</p>
    </el-card>
  </div>

</template>

<script>
import Loading from "./loading.vue";
import {User} from "@element-plus/icons-vue";
import {ElMessage} from 'element-plus'
import axios from "axios";

export default {
  name: 'Login',
  components: {User, Loading},
  data() {
    return {
      loginForm: {
        username: '',
        password: ''
      },
      icp:'http://beian.miit.gov.cn',
      RegForm: {
        usernum: '',
        password: '',
        qq: '',
        email: '',
        capnum: '',
        capnumtext: '点击发送',
        capnumtype: 'primary',
        capnumsta: false,
      },
      ForForm:{
        email: '',
        capnum: '',
        capnumtext: '点击发送',
        capnumtype: 'primary',
        capnumsta: false,
        pwd:null
      },
      errorMessage: '' // 添加一个用于存储错误消息的变量
    };
  },

  methods: {
    changesta() {
      sessionStorage.setItem('isLoggedIn', false)
    },
    joinicp(){
      window.location.href=this.icp;
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
    login() {
      const user_num = this.loginForm.username;
      const user_pwd = this.loginForm.password;
      const data = {
        token: 'ab321818',
        user_pwd: user_pwd,
        user_num: user_num,
      };

      // Send data using POST request
      fetch('https://server.skydreamclub.cn/CheckUser.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
          .then(response => response.json())
          .then(data => {
            if (data.status == '200') {
              this.showSuccessNotification("登录成功");
              sessionStorage.setItem('isLoggedIn', true);
              sessionStorage.setItem('user_num', user_num);
              sessionStorage.setItem("user_grade", data.data.grade);
              sessionStorage.setItem("user_email", data.data.email);
              sessionStorage.setItem("user_email", data.data.email);
              sessionStorage.setItem("user_qq", data.data.qq);
              this.$router.push('/');
            } else {
              alert("账号/密码错误");
            }

          })

          .catch((error) => {
            console.error('Error:', error.message);
            alert("账号/密码错误");
          });
    },
    checkUsernum() {
      if (this.RegForm.usernum.length === 4) {
        if (!/^\d{4}$/.test(this.RegForm.usernum)) {
          alert("必须为四位数字");
        }
        this.checkRegistration(this.RegForm.usernum);
      }
    },
    checkQQnum() {
      if (this.inputTimeoutqq) {
        clearTimeout(this.inputTimeoutqq);
      }

      // 设置一个新的timeout，在用户停止输入500毫秒后调用checkQQnum方法
      this.inputTimeoutqq = setTimeout(() => {
        this.checkQQ(this.RegForm.qq);
      }, 500);
    },
    checkemailnum() {
      if (this.inputTimeoutemail) {
        clearTimeout(this.inputTimeoutemail);
      }

      // 设置一个新的timeout，在用户停止输入500毫秒后调用checkQQnum方法
      this.inputTimeoutemail = setTimeout(() => {
        this.checkemail(this.RegForm.email);
      }, 500);
    },
    checkRegistration(usernum) {
      axios.post('https://server.skydreamclub.cn/CheckCallsignIsUnuse.php', {token: 'ab321818', user_num: usernum})
          .then(response => {
            // 处理API响应，例如：
            if (response.data.status == '200') {
              alert("此呼号已被注册");
              this.RegForm.usernum = null;
            } else {

              this.$message({
                message: '该呼号可用',
                type: 'success'
              });
            }
          })
          .catch(error => {
            console.error(error);
            this.$message({
              message: '检查呼号失败，请稍后再试',
              type: 'error'
            });
          });
    },
    checkQQ(qq) {
      axios.post('https://server.skydreamclub.cn/CheckQQIsUnuse.php', {token: 'ab321818', user_qq: qq})
          .then(response => {
            // 处理API响应，例如：
            if (response.data.status == '200') {
              alert("此QQ已被注册");
              this.RegForm.qq = null;
            } else {

              this.$message({
                message: '该呼号可用',
                type: 'success'
              });
            }
          })
          .catch(error => {
            console.error(error);
            this.$message({
              message: '检查呼号失败，请稍后再试',
              type: 'error'
            });
          });
    },
    checkemail(email) {
      axios.post('https://server.skydreamclub.cn/CheckEmailIsUnuse.php', {token: 'ab321818', user_email: email})
          .then(response => {
            // 处理API响应，例如：
            if (response.data.status == '200') {
              alert("此邮箱已被注册");
              this.RegForm.email = null;
            } else {

              this.$message({
                message: '该呼号可用',
                type: 'success'
              });
            }
          })
          .catch(error => {
            console.error(error);
            this.$message({
              message: '检查呼号失败，请稍后再试',
              type: 'error'
            });
          });
    },
    sendcapnum() {
      let url = 'https://server.skydreamclub.cn/captcha.php?email=' + this.RegForm.email + '&user=xxxx' + this.RegForm.email;
      fetch(url)
          .then(response => response.json())
          .then(data => {
            if (data && data.verificationCode) {
              let capnum = data.verificationCode;
              sessionStorage.setItem("capnum", capnum);
              this.RegForm.capnumtext = '已发送';
              this.RegForm.capnumtype = 'warning'
              this.RegForm.capnumsta = true;
            } else {
              throw new Error('Invalid response format from captcha.php');
            }
          })
          .catch(error => {
            console.error('Error fetching captcha:', error);
          });
    },
    sendcapnumfor() {
      let url = 'https://server.skydreamclub.cn/captcha.php?email=' + this.ForForm.email + '&user=xxxx' + this.ForForm.email;
      fetch(url)
          .then(response => response.json())
          .then(data => {
            if (data && data.verificationCode) {
              let capnum = data.verificationCode;
              sessionStorage.setItem("capnum", capnum);
              this.ForForm.capnumtext = '已发送';
              this.ForForm.capnumtype = 'warning'
              this.ForForm.capnumsta = true;
            } else {
              throw new Error('Invalid response format from captcha.php');
            }
          })
          .catch(error => {
            console.error('Error fetching captcha:', error);
          });
    },
    reg() {
      if (this.RegForm.qq != null && this.RegForm.email != null && this.RegForm.usernum != null && this.RegForm.password != null && this.RegForm.capnum != null) {
        if (this.RegForm.capnum == sessionStorage.getItem("capnum")) {
          const data = {
            token: 'ab321818',
            user_pwd: this.RegForm.password,
            user_num: this.RegForm.usernum,
            user_email: this.RegForm.email,
            user_qq: this.RegForm.qq
          };

          // Send data using POST request
          fetch('https://server.skydreamclub.cn/v4/RegisterNewUser.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
          })
              .then(response => response.json())
              .then(data => {
                if (data.status === '200') {
                  alert('注册成功');
                  sessionStorage.setItem("user_num", this.RegForm.usernum);
                  sessionStorage.setItem("user_grade", this.RegForm.grade);
                  sessionStorage.setItem("user_email", this.RegForm.email);
                  sessionStorage.setItem("user_qq", this.RegForm.qq);
                  this.$router.push('/');
                }
              })

              .catch((error) => {
                console.error('Error:', error.message);
                this.showErrorNotification("注册失败");
              });
        } else {
          alert("验证码错误");
        }


      } else {
        alert('请填写所有信息，谢谢！');
      }
    },
    forget() {
      if (this.ForForm.email != null&&this.ForForm.pwd!=null) {
        if (this.ForForm.capnum == sessionStorage.getItem("capnum")) {
          const data = {
            token: 'ab321818',
            email: this.ForForm.email,
            password: this.ForForm.pwd
          };

          // Send data using POST request
          fetch('https://server.skydreamclub.cn/ForgetUserPassword.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
          })
              .then(response => response.json())
              .then(data => {
                if (data.status === '200') {
                  alert('重置成功');
                  window.location.reload();
                }
              })

              .catch((error) => {
                console.error('Error:', error.message);
                this.showErrorNotification("注册失败");
              });
        } else {
          alert("验证码错误");
        }


      } else {
        alert('请填写所有信息，谢谢！');
      }
    },
  },
  mounted() {
    this.changesta();
  },
};

</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(90deg, #86d0d1, #1134ff);
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 9999; /* 设置一个较高的 z-index 值以确保它在其他内容之上 */
}

.main {
  width: 600px;
  height: 700px;
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


h1.logo-login {
  display: flex;
  justify-content: center;
}

h1.logo-login img {
  width: 100%; /* Ensure the image takes up 100% of the container's width */
  max-width: 300px; /* Set a maximum width to maintain a reasonable size */
  height: auto; /* Maintain aspect ratio */
}
</style>
