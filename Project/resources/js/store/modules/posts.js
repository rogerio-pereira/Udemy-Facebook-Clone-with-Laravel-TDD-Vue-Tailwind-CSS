const state = {
    posts: null,
    postsStatus: null,
    postMessage: '',
};

const getters = {
    posts: state => {
        return state.posts;
    },
    postsStatus: state => {
        return {
            postsStatus: state.postsStatus,
        }
    },
    postMessage: state => {
        return state.postMessage;
    },
};

const actions = {
    fetchPosts({commit, state}) {
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
    fetchUserPosts({commit, dispatch}, userId) {
        commit('setPostsStatus', 'loading');

        axios.get('/api/users/'+userId+'/posts')
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
                commit('setPostsStatus', 'success');
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
    },
    commentPost({commit, state}, data) {
        axios.post('/api/posts/'+data.postId+'/comment', {body: data.body})
            .then(response => {
                commit('pushComments', {comments: response.data, postKey: data.postKey});
            })
            .catch(error => {
            });
    },
};

const mutations = {
    setPosts(state, posts) {
        state.posts = posts;
    },
    setPostsStatus(state, status) {
        state.postsStatus = status;
    },
    updateMessage(state, message) {
        state.postMessage = message;
    },
    pushPost(state, post) {
        state.posts.data.unshift(post);
    },
    pushLikes(state, data) {
        state.posts.data[data.postKey].data.attributes.likes = data.likes;
    },
    pushComments(state, data) {
        state.posts.data[data.postKey].data.attributes.comments = data.comments;
    },
};

export default {
    state,
    getters,
    actions,
    mutations
}