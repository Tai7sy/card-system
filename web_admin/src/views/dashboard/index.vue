<template>
  <div class="dashboard-container">
    <admin-dashboard/>
  </div>
</template>

<script>
  import AdminDashboard from './admin/index';

  export default {
    name: 'dashboard',
    components: { AdminDashboard }
  }
</script>
