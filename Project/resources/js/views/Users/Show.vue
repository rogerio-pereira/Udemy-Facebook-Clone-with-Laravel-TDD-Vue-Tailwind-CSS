<template>
    <div class=''>
        <div class='w-100 h-64 overflow-hidden'>
            <img src='https://i.ytimg.com/vi/dXM6i5-sdVg/maxresdefault.jpg' class='object-cover w-full'>
        </div>
    </div>
</template>

<script>
    export default {
        'name': 'Show',
        'data': () => {
            return {
                user: null,
                loading: true,
            }
        },
        mounted(){
            axios.get('/api/users/'+this.$route.params.userId)
                .then(response => {
                    this.user = response.data;
                })
                .catch(error => {
                    console.log('Unable to fetch user from server.');
                })
                .finally(() => {
                    this.loading = false;
                });

            axios.get('/api/posts/'+this.$route.params.userId)
                .then(response => {
                    this.posts = response.data;
                })
                .catch(error => {
                    console.log('Unable to fetch post.');
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }
</script>

<style scoped>
</style>
