<script >

import {Star} from "@element-plus/icons-vue";

export default {
  data(){
    return{
      tableData: [
        {tagType: 'success', tagText: '40元/小时', description: '初级飞行员/拥有管制权限'},
        {tagType: 'success', tagText: '60元/小时', description: '高级飞行员/管制时长大于100小时'},
        {tagType: 'success', tagText: '80元/小时', description: 'F1副驾驶/管制时长大于200小时'},
        {tagType: 'success', tagText: '100元/小时', description: 'F2副驾驶/管制时长大于300小时'},
        {tagType: 'success', tagText: '120元/小时', description: 'F3副驾驶/管制时长大于400小时'},
        {tagType: 'success', tagText: '140元/小时', description: '机长/管制时长大于500小时'},
        {tagType: 'success', tagText: '160元/小时', description: '机长兼教员/管制时长大于1000小时'},
      ]
    }
  }
}
</script>

<template><br>
  <el-header class="el-header" style="margin-bottom: 10px; font-size: 25px;">
    <el-icon class="icon-1">
      <star/>
    </el-icon>
    工资细则
  </el-header>
  <el-card>
    <el-table :data="tableData" style="width: 100%">
      <el-table-column prop="status" label="图标" width="150">
        <template v-slot="scope">
          <el-tag :type="scope.row.tagType">{{ scope.row.tagText }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="description" label="描述"></el-table-column>
    </el-table>
    <h4>*注 管制会不定期奖励工资</h4>
  </el-card>
  <br>
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
          <h1>初级飞行员 <el-tag type="primary" size="small">无时间要求</el-tag> </h1>
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
          <h1>F2副驾驶 <el-tag type="primary" size="small">飞行时长大于300小时</el-tag> </h1>
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
          <h1>实习管制  <el-tag type="primary" size="small">需行政部门手动发放</el-tag>  </h1>
          <img src="../assets/grade/InternshipAtc.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>正式管制  <el-tag type="primary" size="small">需行政部门手动发放</el-tag> </h1>
          <img src="../assets/grade/FormalAtc.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>教员  <el-tag type="primary" size="small">需行政部门手动发放</el-tag> </h1>
          <img src="../assets/grade/ins.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>管制局副局长  <el-tag type="primary" size="small">需行政部门手动发放</el-tag>  </h1>
          <img src="../assets/grade/DeputyDirector.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>管制局局长  <el-tag type="primary" size="small">需行政部门手动发放</el-tag>  </h1>
          <img src="../assets/grade/managingdirector.png" width="30%" alt="">
        </div>
        <br>
        <div>
          <h1>行政部门 <el-tag type="primary" size="small">平台管理</el-tag>  </h1>
          <img src="../assets/grade/adm.png" width="30%" alt="">
        </div>
        <br>
      </el-card>
    </el-col>

  </el-row>
</template>

<style scoped>

</style>