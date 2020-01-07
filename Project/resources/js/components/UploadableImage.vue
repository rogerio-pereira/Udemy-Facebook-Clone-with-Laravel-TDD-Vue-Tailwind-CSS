<template>
    <div class=''>
        <img 
            src='https://i.ytimg.com/vi/dXM6i5-sdVg/maxresdefault.jpg' 
            class='object-cover w-full'
            ref='userImage'
        >
    </div>
</template>

<script>
    import Dropzone from 'dropzone';
    export default {
        'name': 'UploadableImage',
        data: () => {
            return {
                dropzone: null,
            }
        },
        props: [
            'imageWidth',
            'imageHeight',
            'location',
        ],
        mounted() {
            this.dropzone = new Dropzone(this.$refs.userImage, this.settings);
        },
        computed: {
            settings() {
                return {
                    paramName: 'image',
                    url: '/api/userImages',
                    accepted: 'image/*',
                    params: {
                        'width': this.imageWidth,
                        'height': this.imageHeight,
                        'location': this.location,
                    },
                    headers: {
                        'X-CSRF-TOKEN': document.head.querySelector('meta[name=csrf-token]').content,
                    },
                    success: (e, res) => {
                        alert('uploaded');
                    },
                };
            }
        },
    }
</script>

<style scoped>
</style>
