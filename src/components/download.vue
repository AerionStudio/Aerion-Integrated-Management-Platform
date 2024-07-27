<template>
  <div>
    <el-header  v-if="Official" class="el-header" style="margin-bottom: 10px; font-size: 25px;">
      <el-icon class="icon-1">
        <Download />
      </el-icon>
      软件下载
    </el-header>
    <br>
    <el-card v-if="Official">
      <el-table :data="Official" border style="width: 100%">
        <el-table-column prop="name" label="名称" />
        <el-table-column prop="introduce" label="介绍" />
        <el-table-column prop="version" label="版本" />
        <el-table-column prop="manufacturers" label="开发商" />
        <el-table-column label="下载">
          <template v-slot="scope">
            <el-button type="primary" @click="downloadFile(scope.row.link)">
              下载
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
    <br><br><br>
    <el-header class="el-header" v-if="PublicBeta" style="margin-bottom: 10px; font-size: 25px;">
      <el-icon class="icon-1">
        <Download />
      </el-icon>
      公测版软件下载
    </el-header>
    <br>
    <el-card v-if="PublicBeta">
      <el-table :data="PublicBeta" border style="width: 100%">
        <el-table-column prop="name" label="名称" />
        <el-table-column prop="introduce" label="介绍" />
        <el-table-column prop="version" label="版本" />
        <el-table-column prop="manufacturers" label="开发商" />
        <el-table-column label="下载">
          <template v-slot="scope">
            <el-button type="primary" @click="downloadFile(scope.row.link)">
              下载
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <loading v-if="loading" />
    <div v-if="error" class="error-message">{{ error }}</div>
  </div>
</template>

<script>
import { Download } from '@element-plus/icons-vue';
import axios from 'axios';
import Loading from "./loading.vue"; // Ensure Loading component is created and properly imported

export default {
  components: { Loading, Download },
  data() {
    return {
      PublicBeta: null,
      Official: null,
      loading: false,
      error: null
    };
  },
  methods: {
    fetchData() {
      this.loading = true;
      this.error = null;
      axios.post('https://server.skydreamclub.cn/GetDownload.php')
          .then(response => {
            this.checkwhitelist(response.data);
          })
          .catch(error => {
            this.error = 'Error fetching data';
            console.error('Error fetching data:', error);
          })
          .finally(() => {
            this.loading = false;
          });
    },
    downloadFile(link) {
      window.location.href = link;
    },
    async checkwhitelist(inputdata) {
      try {
        const requestData = {
          token: 'ab321818',
          usernum: sessionStorage.getItem("user_num")
        };
        const response = await axios.post('https://server.skydreamclub.cn/CheckWhitelist.php', requestData);
        const data = response.data;

        if (data.status === '202') {
          this.Official = inputdata.Official;
          this.PublicBeta = inputdata.PublicBeta;
        } else {
          this.Official = inputdata.Official;
        }
      } catch (error) {
        this.error = 'Error fetching event data';
        console.error('Error fetching event data:', error);
      }
    }
  },
  mounted() {
    this.fetchData();
  }
};
</script>

<style scoped>
.error-message {
  color: red;
  font-size: 16px;
  margin-top: 10px;
}
</style>
