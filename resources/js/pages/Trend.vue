<template>
  <div>
    <trend
      :data="[0, 2, 5, 9, 5, 10, 3, 5, 0, 0, 1, 8, 2, 9, 0]"
      :gradient="['#6fa8dc', '#42b983', '#2c3e50']"
      auto-draw
      smooth
    >
  </trend>
  <apexchart width="500" type="bar" :options="chartOptions" :series="series"></apexchart>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  data() {
    return {
      date: new Date(),
      data: {},
      chartOptions: {
        chart: {
          id: 'vuechart-example'
        },
        xaxis: {
          categories: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998]
        }
      },
      series: [{
        name: 'series-1',
        data: [30, 40, 35, 50, 49, 60, 70, 91]
      }],
    }
  },
  methods: {
    get_events(month) {
      if (!month) {
        month = this.date.getMonth()
      }
      month++
      return axios
        .get('/get_events/'+month)
        .then(({ data }) => {
          this.set_events(data)
        })
    },
    set_events(data) {
      console.log(data)
    },
  },
  created: function() {
    this.get_events()
  },
  computed: {
    ...mapGetters([
      'isLogged'
    ]),
    user() {
      return this.$store.state.user.user.name
    }
  },
}
</script>
