import { app, plugin } from '@inertiajs/inertia-vue'
import { InertiaProgress } from '@inertiajs/progress'
import Vue from 'vue'

Vue.use(plugin)

InertiaProgress.init()
const el = document.getElementById('app')

new Vue({
  render: h => h(app, {
    props: {
      initialPage: JSON.parse(el.dataset.page),
      // resolveComponent: name => require(`@/Pages/${name}`).default,
      resolveComponent: name => import(`@/Pages/${name}`).then(module => module.default),
    },
  }),
}).$mount(el)
