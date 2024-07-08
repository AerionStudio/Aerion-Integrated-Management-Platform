<script setup lang="ts">

import {Star} from "@element-plus/icons-vue";
</script>

<template><br>
  <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
    <el-icon class="icon-1">
      <star/>
    </el-icon>
    等级信息
  </el-header>
  <br>
  <el-row :gutter="20">
    <el-col :span="12">
      <div class="grid-content ep-bg-purple"/>
      <el-card>
        <div>
          <h1>初级飞行员 <el-tag type="primary" size="small">无时间要求</el-tag>  </h1>
          <img src="../assets/grade/Juniorpilot.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>高级飞行员 <el-tag type="primary" size="small">飞行时长大于100小时</el-tag></h1>
          <img src="../assets/grade/SeniorPilot.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>F1副驾驶 <el-tag type="primary" size="small">飞行时长大于200小时</el-tag></h1>
          <img src="../assets/grade/F1.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>F2副驾驶 <el-tag type="primary" size="small">飞行时长大于300小时</el-tag></h1>
          <img src="../assets/grade/F2.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>F3副驾驶 <el-tag type="primary" size="small">飞行时长大于400小时</el-tag></h1>
          <img src="../assets/grade/F3.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>机长 <el-tag type="primary" size="small">飞行时长大于500小时</el-tag></h1>
          <img src="../assets/grade/captain.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>机长兼教员 <el-tag type="primary" size="small">飞行时长大于1000小时</el-tag></h1>
          <img src="../assets/grade/Captainandinstructor.png" width="30%" alt="">
        </div>
        <br>
      </el-card>
    </el-col>
    <el-col :span="12">
      <div class="grid-content ep-bg-purple"/>
      <el-card>
        <div>
          <h1>无管制权限 <el-tag type="primary" size="small">默认管制权限</el-tag></h1>
          <img src="../assets/grade/noaatc.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>实习管制  <el-tag type="primary" size="small">需行政部门手动发放</el-tag></h1>
          <img src="../assets/grade/InternshipAtc.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>正式管制  <el-tag type="primary" size="small">需行政部门手动发放</el-tag></h1>
          <img src="../assets/grade/FormalAtc.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>教员  <el-tag type="primary" size="small">需行政部门手动发放</el-tag></h1>
          <img src="../assets/grade/ins.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>管制局副局长  <el-tag type="primary" size="small">需行政部门手动发放</el-tag></h1>
          <img src="../assets/grade/DeputyDirector.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>管制局局长  <el-tag type="primary" size="small">需行政部门手动发放</el-tag></h1>
          <img src="../assets/grade/managingdirector.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>行政部门 <el-tag type="primary" size="small">平台管理</el-tag></h1>
          <img src="../assets/grade/adm.png" width="30%" alt="">
        </div>
        <br>
      </el-card>
    </el-col>

  </el-row>
</template>

<style scoped>

</style>