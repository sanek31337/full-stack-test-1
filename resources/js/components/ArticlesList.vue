<template>
    <v-app>
        <v-row :style="{'justify-content': 'center'}">
            <v-col cols="12" sm="8" md="6">
                <v-toolbar>
                    <v-toolbar-title>Articles list</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-text-field
                        v-on:keyup.enter="getArticlesList()"
                        v-model="search"
                        append-icon="mdi-magnify"
                        label="Search"
                        single-line
                        hide-details
                    ></v-text-field>
                    <v-progress-linear
                        :active="loading"
                        :indeterminate="loading"
                        absolute
                        bottom
                        color="deep-purple accent-4"
                    ></v-progress-linear>
                </v-toolbar>
                <v-select
                    v-show="categories.length"
                    v-model="selectedCategories"
                    :items="categories"
                    multiple
                    chips
                    label="Filter by categories"
                    @change="getArticlesList()"
                ></v-select>
            </v-col>
        </v-row>
        <v-row v-if="!loading">
            <v-spacer v-if="(articlesList % 4 == 1)"></v-spacer>
            <v-col
                v-for="article in articlesList"
                :key="article.article_id"
                cols="12"
                sm="6"
                md="4"
            >
                <article-info :article="article"></article-info>
            </v-col>
        </v-row>
    </v-app>

</template>

<script>
    import ArticleInfo from "./ArticleInfo";
    export default {
        components: {ArticleInfo},
        data() {
            return {
                articles: [],
                selectedCategories: [],
                categories: [],
                search: null,
                menu: false,
                loading: false
            }
        },
        computed: {
            articlesList: function()
            {
                return this.articles;
            }
        },
        mounted() {
            this.getArticlesList();
            this.getCategoriesList();
        },
        methods: {

            async getCategoriesList(){
                let url = '/api/categories-list';

                window.axios.get(url).then(response => {
                    this.categories = response.data;
                })
                    .catch(function(e){
                        this.error = e;
                    });
            },

            async getArticlesList() {

                this.loading = true;

                let that = this;

                let search = this.search;

                let selectedCategories = this.selectedCategories;

                let url = '/api/articles-list';

                let urlParams = new URLSearchParams();

                if (search)
                {
                    urlParams.append('searchPhrase', search);
                }

                if (selectedCategories.length)
                {
                    urlParams.append('categories', selectedCategories.join(','));
                }

                if (urlParams.toString())
                {
                    url += '?';
                }

                url += urlParams.toString();

                window.axios.get(url).then(response => {
                    let data = response.data;

                    that.articles = that.prepareArticles(data);

                    this.loading = false;
                })
                    .catch(function(e){
                        that.error = e;

                        this.loading = false;
                    });
            },

            prepareArticles(data){

                let preparedArticles = [];

                data.map(article => {
                    let preparedArticle = {};

                    preparedArticle = article;

                    preparedArticle.image = (article.images && article.images[0] ? article.images[0] : '/placeholder-image.png');

                    let primaryCategory = article.categories.filter(function(category){
                        return (category.category_type === 0)
                    });

                    if (primaryCategory.length)
                    {
                        preparedArticle.category = primaryCategory[0].name;
                    }
                    else
                    {
                        preparedArticle.category = article.categories[0].name;
                    }

                    preparedArticles.push(preparedArticle);
                });

                return preparedArticles;
            }
        }
    }
</script>
