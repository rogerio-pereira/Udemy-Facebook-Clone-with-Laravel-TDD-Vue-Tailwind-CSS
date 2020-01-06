import Vue from 'vue';
import Vuex from 'vuex';
import User from './modules/user';
import Title from './modules/title';
import Profile from './modules/profile';
import Posts from './modules/posts';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        User,
        Title,
        Profile,
        Posts,
    }
});