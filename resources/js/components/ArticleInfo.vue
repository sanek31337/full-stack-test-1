<template>
    <v-card class="mx-auto">
        <v-img :src="article.image.url" class="mx-auto"></v-img>

        <v-card-title>
            <a href="#">{{article.title}}</a>
        </v-card-title>

        <v-card-text>
            <v-chip
                class="ma-2"
                color="secondary"
                text-color="white"
            >
                {{article.category}}
            </v-chip>
        </v-card-text>

        <v-card-actions>
            <v-btn @click="show = !show">
                {{ show ? 'Hide content' : 'Show content' }}
            </v-btn>
        </v-card-actions>

        <v-expand-transition>
            <div v-show="show">
                <v-card-text>
                    {{article.content}}
                </v-card-text>
            </div>
        </v-expand-transition>
    </v-card>
</template>

<script>
    export default {
        name: "ArticleInfo",
        data(){
            return {
                show: false
            }
        },
        props:{
            article: {
                type: Object
            }
        }
    }
</script>

<style scoped>

</style>
