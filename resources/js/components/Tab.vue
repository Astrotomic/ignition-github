<template>
    <div class="tab-content" id="github">
        <div class="layout-col py-2">
            <v-collapse-group :onlyOneActive="true">
                <v-collapse-wrapper v-if="readme">
                    <div v-collapse-toggle>README</div>
                    <div v-collapse-content v-html="readme"></div>
                </v-collapse-wrapper>
            </v-collapse-group>
        </div>
    </div>
</template>

<script>
    import GitHub from 'github-api';
    import showdown from 'showdown';
    import hljs from 'highlight.js';
    import 'highlight.js/styles/github.css';

    export default {
        props: ['meta'],

        inject: ['report'],

        data () {
            return {
                readme: null,
            }
        },
        mounted () {
            // ToDo: get token from config
            const config = require('../../../github.json');
            const gh = new GitHub({
                token: config.token,
            });
            const repo = gh.getRepo(config.vendor, config.repo);
            console.log(repo);
            repo.getReadme().then((response) => {
                this.readme = new showdown.Converter({tables: true}).makeHtml(atob(response.data.content));
            });
        },
        updated () {
            document.querySelectorAll('#github.tab-content pre > code').forEach((block) => {
                hljs.highlightBlock(block);
            });
        },
    }
</script>

<style>
    #github.tab-content a {
        color: var(--purple-400);
    }

    #github.tab-content a:hover {
        color: var(--purple-500);
    }
</style>
