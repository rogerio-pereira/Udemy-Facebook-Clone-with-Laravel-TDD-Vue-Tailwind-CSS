const state = {
    user: null,
    userStatus: null,
    posts: null,
    postsStatus: null,
};

const getters = {
    user: state => {
        return state.user;
    },
    posts: state => {
        return state.posts;
    },
    status: state =>{
        return {
            user: state.userStatus,
            posts: state.postsStatus,
        }
    },
    friendship: state => {
        return state.user.data.attributes.friendship;
    },
    friendButtonText: (state, getters, rootState) => {
        if(rootState.User.user.data.user_id === state.user.data.user_id) {
            return '';
        }
        else if (getters.friendship === null) {
            return 'Add Friend';
        } else if (getters.friendship.data.attributes.confirmed_at === null
            && getters.friendship.data.attributes.friend_id !== rootState.User.user.data.user_id) {
            return 'Pending Friend Request';
        } else if (getters.friendship.data.attributes.confirmed_at !== null) {
            return '';
        }

        return 'Accept';
    }
};

const actions = {
    fetchUser({commit, dispatch}, userId) {
        commit('setUserStatus', 'loading');

        axios.get('/api/users/'+userId)
            .then(response => {
                commit('setUser', response.data);
                commit('setUserStatus', 'success');
            })
            .catch(error => {
                commit('setUserStatus', 'error');
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
    sendFriendRequest({commit, getters}, friendId) {
        if(getters.friendButtonText !== 'Add Friend') {
            return;
        }

        axios.post('/api/friendRequest', {'friend_id': friendId})
            .then(response => {
                commit('setUserFriendship', response.data);
            })
            .catch(error => {
            });
    },
    acceptFriendRequest({commit, state}, userId) {
        axios.post('/api/friendRequestResponse', {'user_id': userId, 'status': 1})
            .then(response => {
                commit('setUserFriendship', response.data);
            })
            .catch(error => {
            });
    },
    ignoreFriendRequest({commit, state}, userId) {
        axios.delete('/api/friendRequestResponse/delete', {data: {'user_id': userId}})
            .then(response => {
                commit('setUserFriendship', null);
            })
            .catch(error => {
            });
    }
};

const mutations = {
    setUser(state, user) {
        state.user = user;
    },
    setPosts(state, posts) {
        state.posts = posts;
    },
    setUserFriendship(state, friendship) {
        state.user.data.attributes.friendship = friendship;
    },
    setUserStatus(state, status) {
        state.userStatus = status;
    },
    setPostsStatus(state, status) {
        state.postsStatus = status;
    },
};

export default {
    state,
    getters,
    actions,
    mutations
}