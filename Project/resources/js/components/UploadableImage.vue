<template>
    <div class=''>
        <img 
            :src="imageObject.data.attributes.path"
            :alt='alt'
            :class='classes'
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
                uploadedImage: null,
            }
        },
        props: [
            'imageWidth',
            'imageHeight',
            'location',
            'userImage',
            'classes',
            'alt',
        ],
        mounted() {
            this.dropzone = new Dropzone(this.$refs.userImage, this.settings);
        },
        computed: {
            settings() {
                return {
                    paramName: 'image',
                    url: '/api/userImages',
                    acceptedFiles: 'image/*',
                    params: {
                        'width': this.imageWidth,
                        'height': this.imageHeight,
                        'location': this.location,
                    },
                    headers: {
                        'X-CSRF-TOKEN': document.head.querySelector('meta[name=csrf-token]').content,
                    },
                    success: (e, res) => {
                        this.uploadedImage = res;
                    },
                };
            },
            imageObject() {
                return this.uploadedImage || this.userImage;
            }
        },
    }
</script>

<style scoped>
</style>
