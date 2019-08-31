<template>
    <div class="tab-content" id="github">
        <div class="layout-col py-2">
            <v-collapse-group :onlyOneActive="true">
                <v-collapse-wrapper v-if="readme">
                    <div v-collapse-toggle class="font-medium">Readme</div>
                    <div v-collapse-content v-html="readme" class="markdown"></div>
                </v-collapse-wrapper>
                <v-collapse-wrapper>
                    <div v-collapse-toggle class="font-medium">Issues</div>
                    <div v-collapse-content>
                        <div v-if="!issues.length">
                            no issues found
                        </div>
                        <div v-if="issues.length" v-for="issue of issues" class="py-2">
                            <div>
                                <a :href="issue.html_url" target="_blank" class="font-medium hover:text-purple-500" v-html="issue.title"></a>
                                <span class="text-gray-400">by</span>
                                <a :href="issue.user.html_url" target="_blank" class="text-gray-500 hover:text-purple-500">{{issue.user.login}}</a>
                            </div>
                            <div class="text-xs text-gray-400">
                                <span class="mr-2">{{issue.state}}</span>
                                <span class="mr-2">comments: {{issue.comments}}</span>
                            </div>
                            <div>
                                <span v-for="label of issue.labels" class="mr-1 font-mono text-sm text-gray-400">{{label.name}}</span>
                            </div>
                        </div>
                    </div>
                </v-collapse-wrapper>
                <v-collapse-wrapper>
                    <div v-collapse-toggle class="font-medium">create Issue in <em>{{config.vendor}}/{{config.repo}}</em></div>
                    <div v-collapse-content>
                        <label for="issue[title]">title</label>
                        <input type="text" name="issue[title]" v-model.trim="issue.title" class="mt-2 mb-2 w-full py-2 px-2 border-b border-tint-400" />

                        <label for="issue[labels]">labels</label>
                        <input type="text" name="issue[labels]" v-model.trim="issue.labels" class="mt-2 mb-2 w-full py-2 px-2 border-b border-tint-400" />

                        <label for="issue[body]">body</label>
                        <textarea name="issue[body]" v-model.trim="issue.body" class="mt-2 mb-2 w-full py-2 px-2 border-b border-tint-400" rows="10"></textarea>

                        <p v-if="issue.error" class="py-2 text-sm">{{issue.error}}</p>
                        <button type="button" v-on:click="submitIssue" class="inline-block text-purple-400 hover:text-purple-50">create issue</button>
                        <a :href="issue.url" v-if="issue.url" target="_blank" class="ml-2 inline-block text-purple-400 hover:text-purple-50">open issue</a>
                    </div>
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
        gh: null,

        props: ['meta'],

        inject: ['report'],

        data () {
            return {
                config: null,
                readme: null,
                issues: [],
                issue: {
                    title: '['+this.report.stage+'] '+this.report.message,
                    body: ''
                        +'|   | version |'+'\n'
                        +'|---|---|'+'\n'
                        +'| Laravel | '+this.report.framework_version+' |'+'\n'
                        +'| '+this.report.language+' | '+this.report.language_version+' |'+'\n'
                        +'\n'
                        +'| Request | value |'+'\n'
                        +'|---|---|'+'\n'
                        +'| IP | '+this.report.context.request.ip+' |'+'\n'
                        +'| Method | '+this.report.context.request.method+' |'+'\n'
                        +'| URL | '+this.report.context.request.url+' |'+'\n'
                        +'| UA | '+this.report.context.request.useragent+' |'+'\n'
                        +'\n'
                        +'> `'+this.report.exception_class+'`'+'\n'
                        +'> '+this.report.message+'\n'
                        +'\n'
                        +'**StackTrace:**'+'\n'
                        +this.report.stacktrace.map(trace => {
                            return '* `'+(trace.class ? trace.class+'::' : '')+trace.method+'()`'+'\n'+trace.relative_file+':'+trace.line_number
                        }).join('\n')
                    ,
                    labels: 'bug',
                    url: null,
                    error: null,
                }
            }
        },
        mounted () {
            this.config = this.meta.config;
            this.gh = new GitHub({
                token: this.config.token,
            });
            const repo = this.gh.getRepo(this.config.vendor, this.config.repo);
            repo.getReadme().then(response => {
                this.readme = new showdown.Converter({tables: true}).makeHtml(atob(response.data.content));
            });

            const search = this.gh.search({q: 'repo:'+this.config.vendor+'/'+this.config.repo+' '+this.report.message});
            search.forIssues().then(response => {
                this.issues = response.data;
            });
        },
        updated () {
            document.querySelectorAll('#github.tab-content pre > code').forEach((block) => {
                hljs.highlightBlock(block);
            });
        },
        methods: {
            submitIssue: function () {
                console.log(this.issue);

                if(!this.issue.title) {
                    this.issue.error = 'You have to provide a title';
                    return false;
                }

                if(!this.issue.body) {
                    this.issue.error = 'You have to provide a body';
                    return false;
                }

                this.issue.error = null;

                this.gh.getIssues(this.config.vendor, this.config.repo).createIssue({
                    title: this.issue.title,
                    body: this.issue.body,
                    labels: this.issue.labels.split(','),
                }).then(response => {
                    if(response.status !== 201) {
                        return false;
                    }

                    this.issue.url = response.data.html_url;
                    this.issue.title = null;
                    this.issue.body = null;
                    this.issue.labels = null;
                })
            },
        }
    }
</script>

<style>
    #github.tab-content .markdown a {
        color: var(--purple-400);
    }

    #github.tab-content .markdown a:hover {
        color: var(--purple-500);
    }
</style>
