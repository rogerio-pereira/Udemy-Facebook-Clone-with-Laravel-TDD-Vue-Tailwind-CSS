const state = {
    newsPosts: null,
    newsPostsStatus: null,
    postMessage: '',
};

const getters = {
    newsPosts: state => {
        return state.newsPosts;
    },
    newsPostsStatus: state => {
        return {
            newsPostsStatus: state.newsPostsStatus,
        }
    },
    postMessage: state => {
        return state.postMessage;
    },
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
    },
    postMessage({commit, state}) {
        commit('setPostsStatus', 'loading');

        axios.post('/api/posts', {body: state.postMessage})
            .then(response => {
                commit('pushPost', response.data);
                commit('updateMessage', '');
            })
            .catch(error => {
            });
    },
    likePost({commit, state}, data) {
        axios.post('/api/posts/'+data.postId+'/like')
            .then(response => {
                commit('pushLikes', {likes: response.data, postKey: data.postKey});
            })
            .catch(error => {
            });
    }
};

const mutations = {
    setPosts(state, posts) {
        state.newsPosts = posts;
    },
    setPostsStatus(state, status) {
        state.newsPostsStatus = status;
    },
    updateMessage(state, message) {
        state.postMessage = message;
    },
    pushPost(state, post) {
        state.newsPosts.data.unshift(post);
    },
    pushLikes(state, data) {
        state.newsPosts.data[data.postKey].data.attributes.likes = data.likes;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
}