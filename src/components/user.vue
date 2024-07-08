<template>
  <el-card>
    <el-loading :visible="loading">
      <!-- Search Inputs -->
      <div class="search-container">
        <el-input v-model="filters.user_num" placeholder="搜索呼号" class="search-input"></el-input>
        <div class="search-divider"></div>
        <el-input v-model="filters.user_email" placeholder="搜索邮箱" class="search-input"></el-input>
      </div>

      <!-- Table -->
      <el-table :data="filteredData" height="80vh" style="width: 100%;" :default-sort="defaultSort">
        <!-- Columns -->
        <el-table-column prop="user_num" label="呼号" width="180" sortable :sort-method="sortHandler"></el-table-column>
        <el-table-column prop="user_email" label="邮箱" width="300" sortable :sort-method="sortHandler"></el-table-column>
        <el-table-column prop="user_grade" label="权限" sortable :sort-method="gradeSortMethod"></el-table-column>
        <el-table-column fixed="right" label="操作">
          <template v-slot="{ row }">
            <el-button v-if="showButton" type="primary" round @click="handleButtonClick(row.user_num)" size="small">
              编辑
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-loading>
  </el-card>
</template>
<script>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

export default {
  name: 'users',

  data() {
    return {
      eventsData: [],
      showButton: true,
      loading: false,
      filters: {
        user_num: '',
        user_email: '',
      },
      defaultSort: {
        prop: 'user_num',
        order: 'ascending',
      },
    };
  },

  computed: {
    filteredData() {
      let filteredData = [...this.eventsData];
      for (const key in this.filters) {
        if (this.filters[key]) {
          filteredData = filteredData.filter(item => String(item[key]).toLowerCase().includes(this.filters[key].toLowerCase()));
        }
      }
      return filteredData;
    },
  },

  mounted() {
    this.loadData();
  },

  methods: {
    async loadData() {
      try {
        this.loading = true;
        const response = await axios.post('https://server.skydreamclub.cn/GetUsers.php');
        this.eventsData = response.data;
        console.log(this.eventsData);
      } catch (error) {
        console.error('Error fetching data:', error);
      } finally {
        this.loading = false;
      }
    },

    handleButtonClick(id) {
      this.$router.push({ name: 'UserEditPage', params: { id: id } });
    },

    filterHandler(value, row, column) {
      const property = column.property;
      return String(row[property]).toLowerCase().includes(value.toLowerCase());
    },

    sortHandler(a, b, prop) {
      const aValue = a[prop] || '';
      const bValue = b[prop] || '';
      return aValue.localeCompare(bValue, 'zh-CN');
    },


    gradeSortMethod(a, b) {
      const gradesOrder = ['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1', '0'];
      return gradesOrder.indexOf(a.user_grade.toString()) - gradesOrder.indexOf(b.user_grade.toString());
    },
  },
};
</script>

<style scoped>
.search-container {
  display: flex;
  align-items: center;
}

.search-input {
  flex: 1;
}

.search-divider {
  width: 20px;
}
</style>