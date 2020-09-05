<template>
  <div class="columns is-variable is-8">
    <div class="column is-3">
      <b-datepicker
        inline
        v-model="date"
        :first-day-of-week="1"
        :events="events"
        v-on:input="changedate"
        v-on:change-month="get_events">
      </b-datepicker>
    </div>
    <div class="column is-9">
      <div class="box">
        <div class="level">
          <div class="level-left">
            <h1 class="level-item title">{{ headline }}</h1>
          </div>
          <div class="level-right">
            <div class="level-item field">
              <b-switch v-model="edit">Bearbeiten</b-switch>
            </div>
          </div>
        </div>
        <div class="level">
          <b-dropdown v-model="mood" :disabled="!edit" aria-role="list" @change="set_mood_color">
            <button class="button" :class="mood_color" type="button" slot="trigger">
              <template v-if="mood == 1">
                <b-icon icon="emoticon-dead-outline"></b-icon>
              </template>
              <template v-else-if="mood == 3">
                <b-icon icon="emoticon-sad-outline"></b-icon>
              </template>
              <template v-else-if="mood == 5">
                <b-icon icon="emoticon-neutral-outline"></b-icon>
              </template>
              <template v-else-if="mood == 7">
                <b-icon icon="emoticon-happy-outline"></b-icon>
              </template>
              <template v-else-if="mood == 9">
                <b-icon icon="emoticon-outline"></b-icon>
              </template>
              <template v-else>
                  <span>Stimmung?</span>
              </template>
              <b-icon icon="menu-down"></b-icon>
            </button>

            <b-dropdown-item :value="1" aria-role="listitem">
              <div class="media">
                <b-icon class="media-left" icon="emoticon-dead-outline"></b-icon>
                <div class="media-content">
                  <h3>Schlecht</h3>
                  <small>Schlapp, krank, nicht mein Tag</small>
                </div>
              </div>
            </b-dropdown-item>

            <b-dropdown-item :value="3" aria-role="listitem">
              <div class="media">
                <b-icon class="media-left" icon="emoticon-sad-outline"></b-icon>
                <div class="media-content">
                  <h3>Naja</h3>
                  <small>Gibt bessere Tage</small>
                </div>
              </div>
            </b-dropdown-item>

            <b-dropdown-item :value="5" aria-role="listitem">
              <div class="media">
                <b-icon class="media-left" icon="emoticon-neutral-outline"></b-icon>
                <div class="media-content">
                  <h3>Okay</h3>
                  <small>Ganz okay eben</small>
                </div>
              </div>
            </b-dropdown-item>

            <b-dropdown-item :value="7" aria-role="listitem">
              <div class="media">
                <b-icon class="media-left" icon="emoticon-happy-outline"></b-icon>
                <div class="media-content">
                  <h3>Gut</h3>
                  <small>Mir gehts einfach gut!</small>
                </div>
              </div>
            </b-dropdown-item>

            <b-dropdown-item :value="9" aria-role="listitem">
              <div class="media">
                <b-icon class="media-left" icon="emoticon-outline"></b-icon>
                <div class="media-content">
                  <h3>Mega</h3>
                  <small>Heute k&ouml;nnte ich B&auml;ume f&auml;llen</small>
                </div>
              </div>
            </b-dropdown-item>
          </b-dropdown>
        </div>
        <div v-if="edit">
          <form @submit.prevent="create_entry">
            <trumbowyg v-model="content" :config="config" class="form-control" name="content"></trumbowyg>
            <div class="field">
              <p class="control">
                <button class="button is-success">
                  Send
                </button>
              </p>
            </div>
          </form>
        </div>
        <div v-else>
          <div v-html="content"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  data() {
    return {
      date: new Date(),
      data: {},
      date_formatted: '',
      content: '',
      config: {
        btns: [
          ['formatting'],
          ['strong', 'em', 'del'],
          ['link'],
          ['insertImage'],
          ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
          ['unorderedList', 'orderedList'],
          ['horizontalRule'],
          ['removeformat'],
          ['fullscreen']
        ],
        removeformatPasted: true,
        autogrowOnEnter: true,
        defaultLinkTarget: '_blank'
      },
      edit: false,
      headline: '',
      events: [],
      mood: 0,
      mood_color: 'is-primary'
    }
  },
  methods: {
    create_entry() {
      var datestring = this.date.getFullYear()
        +'-'+('0' + (this.date.getMonth()+1)).slice(-2)
        +'-'+('0' + this.date.getDate()).slice(-2)
      this.$store
        .dispatch('create_entry', {
          date: datestring,
          content: this.content,
          mood: this.mood
        })
        .then(() => {
          this.get_events()
          this.changedate()
        })
        .catch(err => {
          console.log(err)
        })
    },
    changedate(event) {
      // get entry for this.date
      //this.date_formatted = this.date.toISOString().split('T')[0]
      // TODO
      this.get_entry()
    },
    get_entry(datestring) {
      if (!datestring) {
        datestring = this.date.getFullYear()
          +'-'+('0' + (this.date.getMonth()+1)).slice(-2)
          +'-'+('0' + this.date.getDate()).slice(-2)
      }
      return axios
        .post('/entry', {'date': datestring})
        .then(({ data }) => {
          this.set_content(data)
        })
    },
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
      this.events = []
      for (let entry of data) {
        let mood = 'is-primary'
        switch(entry.mood) {
          case 1:
          case 3:
            mood = 'is-danger'
            break
          case 5:
            mood = 'is-warning'
            break
          case 7:
          case 9:
            mood = 'is-success'
            break
          default:
            mood = 'is-primary'
            break
        }
        this.events.push({
          date: new Date(entry.date),
          type: mood
        })
      }
    },
    set_content(data) {
      if (data) {
        this.data = data
        this.content = data.content
        this.mood = data.mood
        let date = new Date(data.date)
        this.headline = 'Eintrag vom '+date.toLocaleDateString()
        this.edit = false
      } else {
        this.data = {}
        this.mood = 0
        let date = new Date(this.date)
        this.headline = 'Kein Eintrag am '+date.toLocaleDateString()+' vorhanden.'
        this.content = ''
        this.edit = true
      }
      this.set_mood_color()
    },
    set_mood_color(selected_mood) {
      if (!selected_mood) {
        selected_mood = this.mood
      }
      switch (selected_mood) {
        case 1: this.mood_color = 'is-danger'; break;
        case 3: this.mood_color = 'is-danger'; break;
        case 5: this.mood_color = 'is-warning'; break;
        case 7: this.mood_color = 'is-success'; break;
        case 9: this.mood_color = 'is-success'; break;
        default: this.mood_color = 'is-primary'; break;
      }
    }
  },
  created: function() {
    this.get_events()
    this.changedate()
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
