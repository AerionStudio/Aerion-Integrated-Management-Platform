<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-aside :width="asideWidth" class="aside-transition">
        <br>
        <!-- Sidebar Content -->
        <el-menu default-active="1" class="el-menu-vertical-demo full-height" hide-text>
          <el-header v-if="showHeader" class="el-header-1 icon">
            菜单
            <hr class="title">
          </el-header>
          <el-menu-item index="1" @click="navigateTo($router, '/home')">
            <el-icon>
              <House/>
            </el-icon>
            <span v-if="!isCollapsed">首页</span>
          </el-menu-item>
          <el-menu-item index="2" @click="navigateTo($router, '/event/now')">
            <el-icon>
              <Notebook/>
            </el-icon>
            <span v-if="!isCollapsed">活动报名</span>
          </el-menu-item>
          <el-menu-item index="3" @click="navigateTo($router, '/map')">
            <el-icon>
              <MapLocation/>
            </el-icon>
            <span v-if="!isCollapsed">在线地图</span>
          </el-menu-item>
          <el-menu-item index="4" @click="navigateTo($router, '/weather')">
            <el-icon>
              <WindPower/>
            </el-icon>
            <span v-if="!isCollapsed">天气查询</span>
          </el-menu-item>
          <el-menu-item index="5" @click="navigateTo($router, '/route')">
            <el-icon>
              <Place/>
            </el-icon>
            <span v-if="!isCollapsed">航路查询</span>
          </el-menu-item>
          <el-menu-item index="6" @click="navigateTo($router, '/atc')">
            <el-icon>
              <Service/>
            </el-icon>
            <span v-if="!isCollapsed">管制局</span>
          </el-menu-item>
          <el-menu-item index="7" @click="navigateTo($router, '/join')">
            <el-icon>
              <Plus/>
            </el-icon>
            <span v-if="!isCollapsed">加入我们</span>
          </el-menu-item>
          <el-menu-item index="8" @click="navigateTo($router, '/download')">
            <el-icon>
              <Download/>
            </el-icon>
            <span v-if="!isCollapsed">下载</span>
          </el-menu-item>
          <el-menu-item index="9" @click="navigateTo($router, '/gradelist')">
            <el-icon>
              <Star/>
            </el-icon>
            <span v-if="!isCollapsed">等级信息</span>
          </el-menu-item>
          <el-menu-item index="10" @click="navigateTo($router, '/user')">
            <el-icon>
              <User/>
            </el-icon>
            <span v-if="!isCollapsed">个人中心</span>
          </el-menu-item>
          <el-menu-item index="11" @click="navigateTo($router, '/info')">
            <el-icon>
              <InfoFilled/>
            </el-icon>
            <span v-if="!isCollapsed">版权信息</span>
          </el-menu-item>
          <p v-if="asideWidth === '300px'" @click="join()" style="padding: 10px">湘ICP备2024046403号-5</p>
        </el-menu>
      </el-aside>
      <!-- Toggle Button -->
      <el-container>
        <el-header class="full-weight header-with-border" height="75px">
          <h1>
            <el-button @click="toggleCollapse">
              <el-icon>
                <Expand v-if="isCollapsed"/>
                <Fold v-else/>
              </el-icon>
            </el-button>
            {{ platform }} 综合管理平台
          </h1>
          <h1 class="logo">
            <img src="./assets/logo.png" alt="" style="width: 5%;height: 5%" class="logo-main">
            <h1 class="logo-text">SKYDREAM CLUB</h1>
          </h1>
          <h1 class="collapse-button"><span class="time" style="padding-right: 10px">UTC {{ currentTime }}</span>
            <el-button @click="toggleTheme">
              <el-icon v-if="isDarkMode" :style="{ cursor: 'pointer' }">
                <Sunny/>
              </el-icon>
              <el-icon v-else :style="{ cursor: 'pointer' }">
                <Moon/>
              </el-icon>
            </el-button>
          </h1>
        </el-header>
        <el-main>
          <!-- Route Content -->
          <router-view></router-view>
        </el-main>
      </el-container>
      <el-backtop :right="100" :bottom="100"/>
    </el-container>
  </div>
</template>

<style>
.aside-transition {
  transition: width 0.3s;
  overflow: hidden;
}

.el-menu-vertical-demo {
  height: calc(100vh - 75px);
  overflow-y: auto;
}

.header-with-border {
  border-bottom: 1px solid #d3d3d3;
}

.full-height {
  height: 100vh;
  overflow: hidden;
}

.full-weight {
  width: 100%;
}

.aside-transition {
  transition: width 0.3s;
}

.collapse-button {
  font-size: 25px;
  position: absolute;
  right: 10px;
  top: 0px;
}

.el-header-1 {
  margin-bottom: 10px;
  font-size: 25px;
}

.el-header-1.icon {
  position: relative;
}

.el-header-1.icon .title {
  position: absolute;
  left: 20px;
  bottom: -10px;
  width: 100px;
  height: 6px;
  background-color: #66b1ff;
  border: #66b1ff;
  border-radius: 10px;
}

h1.logo {
  display: flex;
  justify-content: center;
  margin-top: -73px;
}


h1.logo img {
  width: 100%; /* Ensure the image takes up 100% of the container's width */
  max-width: 300px; /* Set a maximum width to maintain a reasonable size */
  height: auto; /* Maintain aspect ratio */
}

@media (max-width: 900px) {
  h1.logo img {
    display: none; /* Hide the logo for screens narrower than 600px */
  }
  .logo-text{
    display: none;
  }
}

@media (max-width: 800px) {
  .time {
    display: none; /* Hide the logo for screens narrower than 600px */
  }
}
</style>

<script setup lang="ts">
import { toggleDark } from "./composables/index.ts";
import { ref } from "vue";
import { Router } from "vue-router";
import { InfoFilled, MapLocation, Place, Plus, Service, Star, User, WindPower } from "@element-plus/icons-vue";
import { House, Notebook, Picture, Expand, Fold, Sunny, Moon } from "@element-plus/icons-vue";
import Map from "./components/events.vue";
import Info from "./components/info.vue";

const platform = "天梦俱乐部 || SkyDreamClub";
const isCollapsed = ref(false);
const asideWidth = ref('300px');
const user_num = sessionStorage.getItem("user_num");
const formatTime = (date: Date) => {
  const hours = String(date.getUTCHours()).padStart(2, '0');
  const minutes = String(date.getUTCMinutes()).padStart(2, '0');
  const seconds = String(date.getUTCSeconds()).padStart(2, '0');
  return `${hours}:${minutes}:${seconds}`;
};

const currentTime = ref(formatTime(new Date()));
const showHeader = ref(true);

const navigateTo = (router: Router, path: string) => {
  router.push(path);
};

const toggleCollapse = () => {
  isCollapsed.value = !isCollapsed.value;
  asideWidth.value = isCollapsed.value ? '65px' : '300px';
  showHeader.value = !isCollapsed.value;
};

setInterval(() => {
  currentTime.value = formatTime(new Date());
}, 1000);

const isDarkMode = ref(false);

const toggleTheme = () => {
  toggleDark();
  isDarkMode.value = !isDarkMode.value;
};

const join = () => {
  window.location.href = 'http://beian.miit.gov.cn';
};
</script>
