const state = {
    user: null,
    userStatus: null,
    friendButtonText: null,
};

const getters = {
    user: state => {
        return state.user;
    },
    friendship: state => {
        return state.user.data.attributes.friendship;
    },
    friendButtonText: sate => {
        return state.friendButtonText;
    }
};

const actions = {
    fetchUser({commit, dispatch}, userId) {
        commit('setUserStatus', 'loading');

        axios.get('/api/users/'+userId)
            .then(response => {
                commit('setUser', response.data);
                commit('setUserStatus', 'success');
                dispatch('setFriendButton');
            })
            .catch(error => {
                commit('setUserStatus', 'error');
            });
    },
    sendFriendRequest({commit, state}, friendId) {
        commit('setButtontext', 'Loading'); 

        axios.post('/api/friendRequest', {'friend_id': friendId})
            .then(response => {
                commit('setButtontext', 'Pending Friend Request...');
            })
            .catch(error => {
                commit('setButtontext', 'Add Friend');
            });
    },
    setFriendButton({commit, getters}) {
        if(getters.friendship === null) {
            commit('setButtontext', 'Add Friend');
        }
        else if(getters.friendship.data.attributes.confirmed_at === null) {
            commit('setButtontext', 'Pending Friend Request...');
        }
    }
};

const mutations = {
    setUser(state, user) {
        state.user = user;
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