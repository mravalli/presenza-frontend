<template>
  <div class="home">
    <Attendance/>
  </div>
</template>

<script>
// @ is an alias to /src
import Attendance from '@/components/Attendance.vue'

export default {
  name: 'Home',
  components: {
    Attendance
  },
}
</script>
