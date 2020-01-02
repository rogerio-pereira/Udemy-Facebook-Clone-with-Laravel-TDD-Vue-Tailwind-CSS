const state = {
    user: null,
    userStatus: null,
};

const getters = {
    user: state => {
        return state.user;
    },
    friendship: state => {
        return state.user.data.attributes.friendship;
    },
    friendButtonText: (state, getters, rootState) => {
        if(getters.friendship === null) {
            return 'Add Friend';
        }
        else if(getters.friendship.data.attributes.confirmed_at === null) {
            return 'Pending Friend Request...';
        }
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
    sendFriendRequest({commit, state}, friendId) {
        commit('setButtontext', 'Loading'); 

        axios.post('/api/friendRequest', {'friend_id': friendId})
            .then(response => {
                commit('setUserFriendship', response.data);
            })
            .catch(error => {
            });
    }
};

const mutations = {
    setUser(state, user) {
        state.user = user;
    },
    setUserFriendship(state, friendship) {
        state.user.data.attributes.friendship = friendship;
    },
    setUserStatus(state, userStatus) {
        state.userStatus = userStatus;
    },
    setButtontext(state, text) {
        state.friendButtonText = text;
    },
};

export default {
    state,
    getters,
    actions,
    mutations
}