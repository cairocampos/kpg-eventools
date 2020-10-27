import Vue from 'vue'
import Vuex from 'vuex'
import axios from "axios";

Vue.use(Vuex)

const store = new Vuex.Store({
  state: {
    event: {
        title:"",
        started:"",
        description:"",
        localization:"",
        cover:"",
        active:1
    }
  },
  mutations: {
    UPDATE_EVENT (state, payload) {
      state.event = Object.assign(state.event, payload);
      if(payload.started) {
        state.event.started = payload.started.replace(" ", "T");
      }
    }
  },
  actions: {
      createEvent(context, payload) {
        context.commit("UPDATE_EVENT", payload)
        return axios.post("/events", payload);
      },
      resetEvent(context) {
        context.commit("UPDATE_EVENT", {
          title:"",
          started:"",
          description:"",
          localization:"",
          cover:"",
          active:1,          
        });
      }
  }
});

export default store;