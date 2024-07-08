<template>
  <transition name="el-fade-in-linear">
    <div style="text-align: center;padding-top: 100px;padding-right: 150px;padding-left: 150px" class="dsipaly">
      <el-card>
        <h1 style="font-size: 40px">暂无正在报名的活动</h1>
        <h1 style="font-size: 40px">
          <el-button type="primary" @click="handleButtonClick()">查看既往活动</el-button>
        </h1>
      </el-card>
    </div>
  </transition>
</template>

<style>
.el-card {
  border-radius: 20px;
}

.dsipaly:hover {
  transform: translateY(-10px);
  transition: transform 0.5s ease;
}

.el-fade-in-linear-enter-active, .el-fade-in-linear-leave-active {
  transition: opacity 0.5s linear;
}

.el-fade-in-linear-enter, .el-fade-in-linear-leave-to {
  opacity: 0;
}
</style>

<script>
export default {
  methods: {
    handleButtonClick() {
      this.$router.push({name: 'list'});
    }
  }
}
</script>
