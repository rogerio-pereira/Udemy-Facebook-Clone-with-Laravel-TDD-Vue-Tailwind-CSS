const state = {
    newsPosts: null,
    newsPostsStatus: null
};

const getters = {
    newsPosts: state => {
        return state.newsPosts;
    },
    newsPostsStatus: state => {
        return {
            newsPostsStatus: state.newsPostsStatus,
        }
    }
};

const actions = {
    fetchNewsPosts({commit, state}) {
        commit('setPostsStatus', 'loading');

        axios.get('/api/posts')
                .then(response => {
                    commit('setPosts', response.data);
                    commit('setPostsStatus', 'success');
                })
                .catch(error => {
                    commit('setPostsStatus', 'error');
                });
    }
};

const mutations = {
    setPosts(state, posts) {
        state.newsPosts = posts;
    },
    setPostsStatus(state, status) {
        state.newsPostsStatus = status;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
}