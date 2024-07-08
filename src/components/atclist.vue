<script>

import {defineComponent} from "vue";
import {Headset, List, Service} from "@element-plus/icons-vue";
import axios from "axios";

export default {
  data() {
    return {
      tableData: [
        {tagType: 'info', tagText: 'X', description: '无权限'},
        {tagType: 'warning', tagText: 'T', description: '该席位为实习训练阶段'},
        {tagType: 'success', tagText: '√', description: '该席位已放单'},
        {tagType: 'success', tagText: '资深', description: '该席位为资深席位'},
        {tagType: 'primary', tagText: '√', description: '该席位已放单'},
        {tagType: 'primary', tagText: '资深', description: '该席位为资深席位'},
        {tagType: 'danger', tagText: '暂停', description: '该席位因考核/活动不合格暂停'},
        {tagType: 'info', tagText: '休假', description: '管制员提前说明休假'},
      ],
      atcData: [],
      load:true,
      main:false
    }
  },
  methods: {
    async getatcdata() {

      try {
        const response = await axios.post(`https://server.skydreamclub.cn/GetATCs.php`);

        this.atcData = [];
        const data = response.data.data;
        this.atcData = data;
        console.log(data);
      } catch (error) {
        console.error('Error fetching event data:', error);
      }
      this.load = false;
      this.main = true;
    },
    watchuser(id){
      this.$router.push({name: 'users', params: {id: id}});
    },
  },
  mounted() {
    this.getatcdata();
  }
}
</script>

<template>
  <div>
    <div v-show="load" style="text-align: center; padding-top: 100px; padding-right: 150px; padding-left: 150px"
         class="display">
      <el-card>
        <h1 style="font-size: 40px">正在加载数据,请稍等片刻</h1>
        <h1 style="font-size: 40px">waiting...</h1>
      </el-card>
    </div>

    <transition name="el-fade-in-linear">
      <div v-show="main">
        <br>
        <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
          <el-icon class="icon-1">
            <service/>
          </el-icon>
          管制室介绍
        </el-header>
        <br>
        <h1 style="padding: 20px;font-size: 30px">
          SkyDreamClub管制室秉承娱乐和专业的理念，致力于为玩家在工作学习之余提供一个高兴快乐的连飞环境。以机组高兴，管制愉悦，其乐融融为目标，建立一个团结，互助，灵活的平台环境。
        </h1>
        <br>
        <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
          <el-icon class="icon-1">
            <headset/>
          </el-icon>
          教员信息
        </el-header>
        <br>
        <el-row :gutter="20">
          <el-col :span="8" v-for="(item, index) in atcData.teacher" :key="index">
            <div class="grid-content ep-bg-purple"/>
            <el-tooltip
                class="box-item"
                effect="dark"
                :content="'查看 ' + item.callsign  + ' 个人信息'"
                placement="top"

            >
              <el-card @click="watchuser(item.callsign)">
                <div class="content">
                  <el-avatar :size="100" :src=item.avatar
                             class="avatar"/>
                  <div class="text">
                    <h1 style="font-size: 30px">{{ item.callsign }} | {{ item.grade_text }}</h1>
                  </div>
                </div>
              </el-card>
            </el-tooltip>

            <br>
          </el-col>

        </el-row>
        <br><br>
        <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
          <el-icon class="icon-1">
            <headset/>
          </el-icon>
          所有管制
        </el-header>
        <br>
        <el-row :gutter="20">
          <el-col :span="6" v-for="(item, index) in atcData.all" :key="index">
            <div class="grid-content ep-bg-purple"/>
            <el-tooltip
                class="box-item"
                effect="dark"
                :content="'查看 ' + item.callsign + ' 个人信息'"
                placement="top"

            >
              <el-card @click="watchuser(item.callsign)">
                <div class="content">
                  <el-avatar :size="70" :src=item.avatar
                             class="avatar"/>
                  <div class="text">
                    <h1 style="font-size: 20px">{{ item.callsign }} | {{ item.grade_text }}</h1>
                  </div>
                </div>
              </el-card>
            </el-tooltip>

            <br>
          </el-col>

        </el-row>
        <br><br>
        <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
          <el-icon class="icon-1">
            <list/>
          </el-icon>
          管制权限列表
        </el-header>
        <br>
        <el-row :gutter="20">
          <el-col :span="6">
            <div class="grid-content ep-bg-purple"/>
            <el-card>
              <h1 style="font-size: 30px">图例</h1>
              <el-table :data="tableData" style="width: 100%">
                <el-table-column prop="status" label="图标" width="150">
                  <template v-slot="scope">
                    <el-tag :type="scope.row.tagType">{{ scope.row.tagText }}</el-tag>
                  </template>
                </el-table-column>
                <el-table-column prop="description" label="描述"></el-table-column>
              </el-table>
            </el-card>
          </el-col>
          <el-col :span="18">
            <div class="grid-content ep-bg-purple"/>
            <el-card>
              <el-table :data="atcData.all" style="width: 100%;height: 465px">
                <el-table-column prop="callsign" label="用户" width="190" >
                  <template v-slot="scope">
                    <el-tooltip
                        class="box-item"
                        effect="dark"
                        :content="'查看 ' + scope.row.callsign + ' 个人信息'"
                        placement="top"

                    >
                      <span   @click="watchuser(scope.row.callsign)">{{ scope.row.callsign}}</span>
                    </el-tooltip>
                  </template>
                </el-table-column>
                <el-table-column prop="grade_text" label="雷达权限">
                </el-table-column>
                <el-table-column prop="status" label="塔台及以下">
                  <template v-slot="scope">
                    <el-tag :type="scope.row.gradelist[0].type">{{ scope.row.gradelist[0].text }}</el-tag>
                  </template>
                </el-table-column>
                <el-table-column prop="status" label="进近">
                  <template v-slot="scope">
                    <el-tag :type="scope.row.gradelist[1].type">{{ scope.row.gradelist[1].text }}</el-tag>
                  </template>
                </el-table-column>
                <el-table-column prop="status" label="区域" width="150">
                  <template v-slot="scope">
                    <el-tag :type="scope.row.gradelist[2].type">{{ scope.row.gradelist[2].text }}</el-tag>
                  </template>
                </el-table-column>
                <el-table-column prop="status" label="塔台及以下(教员)" width="150">
                  <template v-slot="scope">
                    <el-tag :type="scope.row.gradelist[3].type">{{ scope.row.gradelist[3].text }}</el-tag>
                  </template>
                </el-table-column>
                <el-table-column prop="status" label="进近(教员)	" width="150">
                  <template v-slot="scope">
                    <el-tag :type="scope.row.gradelist[4].type">{{ scope.row.gradelist[4].text }}</el-tag>
                  </template>
                </el-table-column>
                <el-table-column prop="status" label="区域(教员)" width="150">
                  <template v-slot="scope">
                    <el-tag :type="scope.row.gradelist[5].type">{{ scope.row.gradelist[5].text }}</el-tag>
                  </template>
                </el-table-column>
              </el-table>
            </el-card>

          </el-col>

        </el-row>
      </div>
    </transition>
  </div>


</template>

<style scoped>
.card {
  display: flex;
  justify-content: space-between;
}

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