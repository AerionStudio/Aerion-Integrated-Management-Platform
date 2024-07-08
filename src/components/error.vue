<template>
  <div style="text-align: center;padding-top: 100px;padding-right: 150px;padding-left: 150px" class="dsipaly">
    <el-card>
      <h1 style="font-size:40px">坏了，页面出错了</h1>
      <h1 style="font-size:40px">快去拷打技术</h1>
      <el-button size="large" type="danger" style="width: 120px" @click="handleButtonClick()"><el-icon><back/></el-icon> 返回</el-button>
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
      this.$router.push({ name: 'home' });
    }
  }
}
</script>
