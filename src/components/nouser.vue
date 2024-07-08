<template>
  <div style="text-align: center;padding-top: 100px;padding-right: 150px;padding-left: 150px" class="dsipaly">
    <el-card>
      <h1 style="font-size:40px">用户不存在</h1>
      <el-button size="large" type="primary" style="width: 120px" @click="handleButtonClick()"><el-icon><back/></el-icon> 返回</el-button>
      <br><br><br>
    </el-card>
  </div>
</template>

<style>
.el-card{
  border-radius: 20px;
}
.dsipaly:hover {
  transform: translateY(-10px);
  transition: transform 0.5s ease;
}
</style>

<script>
export default {
  methods: {
    handleButtonClick() {
      this.$router.push({ name: 'person' });
    }
  }
}
</script>
