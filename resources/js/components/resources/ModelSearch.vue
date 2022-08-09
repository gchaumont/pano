<template>
    <form id="resource-search" action="" method="get" class="relative inline-block">
        <input class=" text-slate-400 dark:text-slate-100 bg-white dark:bg-slate-800 rounded-md py-2 px-4 min-w-55ch   shadow-sm  dark:bg-slate-800 dark:highlight-white/5 dark:hover:bg-slate-800/80 outline-none" type="text" placeholder="Search..." name="" v-model="search" style="position: sticky; top: 0" @click="activate()" @keydown="activate" @focus="activate()" @blur="deactivate()" ref="searchInput">
        <button type="submit" class="hidden"> </button>
        <div class="absolute shadow-lg top-100 overflow-y-auto max-h-80 inset-x-0 z-10 bg-white dark:bg-slate-700" v-if="active && suggestions?.length">
            <ol>
                <li v-for="suggestion, key in suggestions" @mouseenter="selected=key" @click="select()" class="cursor-pointer whitespace-nowrap flex " v-bind:class="{'bg-slate-200 dark:bg-slate-600': key == selected}">
                    <div class="pl-2 py-1 hidden">{{suggestion.type}}</div>
                    <div class="px-2 w-96  py-1 ">{{suggestion.text}}</div>
                    <div class="px-2 w-full py-1 truncate text-sm text-slate-400 dark:text-slate-300">{{suggestion.description}}</div>
                </li>
            </ol>
        </div>
    </form>
</template>
<script>
export default {
    props: {
        path: {
            type: String,
            required: true,
        }
    },
    emits: ['search'],
    data() {
        return {
            search: '',
            suggestions: [],
            active: false,
            shouldDeactivate: false,
            selected: -1,
            maxItems: 10,
        }
    },
    methods: {
        suggest: function() {
            this.selected = -1;
            setTimeout(() => {
                fetch(this.path + "/suggest?" + new URLSearchParams({ search: this.search + '.', p: this.$refs.searchInput.selectionStart }), { headers: { 'Accept': 'application/json' } })
                    .then(response => response.json().then(json => {
                        this.suggestions = json;
                    }))
            }, 2)
        },
        select: function() {
            this.$refs.searchInput.focus()
            const suggestion = this.suggestions[this.selected];

            this.search = [
                this.search.slice(0, suggestion.start),
                suggestion.text,
                this.search.slice(suggestion.end),
            ].join('')

            if (suggestion.index) {
                setTimeout(() => {
                    this.$refs.searchInput.setSelectionRange(suggestion.index, suggestion.index);
                }, 0)
            }
        },
        activate: function(e) {
            if (e !== undefined && e.hasOwnProperty('key') && e.key == 'Enter') {
                return
            }
            this.shouldDeactivate = false;
            this.active = true;
        },
        deactivate: function() {
            this.shouldDeactivate = true;
            setTimeout(() => {
                // if (this.$refs.searchInput.activeElement) {
                if (this.shouldDeactivate) {
                    this.active = false
                } else {

                }
            }, 150);


        }
    },
    mounted() {
        this.suggest();
        this.$watch(
            () => this.search + this.path,
            () => this.suggest(), { immediate: true }
        );
        this.$watch(
            () => this.path,
            () => this.search = '', { immediate: true }
        );

        this._keyListener = function(e) {

            if (e.key == 'Backspace') { // delete closing brackets if no content between
                var deletedLetter = this.search.slice(this.$refs.searchInput.selectionStart - 1, this.$refs.searchInput.selectionStart);

                var nextLetter = this.search.slice(this.$refs.searchInput.selectionStart, this.$refs.searchInput.selectionStart + 1);

                if ((deletedLetter == '(' && nextLetter == ')') || (deletedLetter == '{' && nextLetter == '}')) {
                    this.search = [
                        this.search.slice(this.$refs.searchInput, 0, this.$refs.searchInput.selectionStart),
                        this.search.slice(this.$refs.searchInput, this.$refs.searchInput.selectionStart)
                    ].join('')
                }

            }

            if (e.key === "(" || e.key == '{' || e.key == '"') { // add closing brackets around selection or emtpy
                var startChar = e.key;
                var endChar = ''
                switch (startChar) {
                    case '(':
                        endChar = ' )'
                        break;
                    case '{':
                        endChar = ' }'
                        break;
                    case '"':
                        endChar = ' "'
                        break;
                }

                e.preventDefault()
                var start = this.$refs.searchInput.selectionStart;
                var end = this.$refs.searchInput.selectionEnd;
                this.search = [
                    this.search.slice(0, start),
                    startChar,
                    this.search.slice(start, end),
                    endChar,
                    this.search.slice(end)
                ].join('')
                setTimeout(() => {

                    this.$refs.searchInput.setSelectionRange(start + 1, end + 1);
                }, 1)
            }


            if (e.key === "Escape") {
                if (this.active) {
                    e.preventDefault();
                    this.active = false;
                }
            }
            if (e.key === "Enter") {
                e.preventDefault();
                if (this.active && this.selected >= 0) {
                    this.select();
                } else {
                    this.deactivate()
                    this.$emit('search', this.search);
                }
            }

            if (e.key === "ArrowDown") {
                e.preventDefault();
                if (this.selected >= this.maxItems - 1) {
                    this.selected = 0;
                } else {
                    this.selected = this.selected + 1;
                }
            }
            if (e.key === "ArrowUp") {
                e.preventDefault();
                if (this.selected <= 0) {
                    this.selected = this.maxItems - 1;
                } else {
                    this.selected = this.selected - 1;
                }
            }


        };
        document.addEventListener('keydown', this._keyListener.bind(this));
    }
}
</script>