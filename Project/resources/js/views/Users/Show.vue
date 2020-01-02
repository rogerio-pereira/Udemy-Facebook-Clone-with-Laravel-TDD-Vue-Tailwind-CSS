<template>
    <div class='flex flex-col items-center'>
        <div class='relative mb-8'>
            <div class='w-100 h-64 overflow-hidden z-10'>
                <img src='https://i.ytimg.com/vi/dXM6i5-sdVg/maxresdefault.jpg' class='object-cover w-full'>
            </div>

            <div class='absolute flex items-center bottom-0 left-0 -mb-8 ml-20 z-20'>
                <div class='w-32'>
                    <img 
                        src='https://www.midlandsderm.com/wp-content/uploads/2019/04/Rachel-R.-Person-760x760.jpg' 
                        class='object-cover w-32 h-32 border-4 border-gray-200 rounded-full shadow-lg'
                    >
                </div>

                <p class='ml-4 text-2xl text-gray-100'>{{user.data.attributes.name}}</p>
            </div>

            <div class='absolute flex items-center bottom-0 right-0 mb-4 mr-12 z-20'>
                <button class='py-1 px-3 bg-gray-400 rounded'>Add Friend</button>
            </div>
        </div>

        <p v-if="postsLoading">Loading posts..</p>
        <Post v-else v-for="post in posts.data" :key='post.data.post_id' :post='post'/>
        <p v-if='!postsLoading && posts.data.lenght < 1'>
            No posts found. Get Started.
        </p>
    </div>
</template>

<script>
    import Post from '../../components/Post';
    import {mapGetters} from 'vuex';

    export default {
        'name': 'Show',
        components: {
            Post
        },
        'data': () => {
            return {
                posts: null,
                postsLoading: true,
            }
        },
        mounted(){
            this.$store.dispatch('fetchUser', this.$route.params.userId);

            axios.get('/api/users/'+this.$route.params.userId+'/posts')
                .then(response => {
                    this.posts = response.data;
                })
                .catch(error => {
                    console.log('Unable to fetch post.');
                })
                .finally(() => {
                    this.postsLoading = false;
                });
        },
        computed: {
            ...mapGetters({
                user: 'user',
            }),
        }
    }
</script>

<style scoped>
</style>
