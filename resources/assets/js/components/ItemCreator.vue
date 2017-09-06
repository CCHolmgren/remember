<template>
    <div>
        <div class="form-group">
            <label>Name the item</label>
            <input class="form-control" type="text" v-model="form.name" placeholder="Name of item">
        </div>

        <div class="form-group">
            <label>What kind of item is it?</label>
            <select class="form-control" v-model="type">
                <option value="" disabled hidden>Select a type</option>
                <option v-for="type in types" :value="type">{{ type }}</option>
                <option value="custom">Custom</option>
            </select>
        </div>
        <div class="form-group" v-if="type == 'custom'">
            <input class="form-control" v-model="form.type" placeholder="Custome type">
        </div>

        <div v-for="(item, index) in form.content">
            <div class="form-group row mb-0" v-if="index == 0">
                <div :class="enableRowRemoval ? 'col-md-5' : 'col-md-6'">
                    <label>Name of row</label>
                </div>
                <div class="col-md-6">
                    <label>Content of row</label>
                </div>
            </div>
            <div class="form-group row">
                <div :class="enableRowRemoval ? 'col-md-5' : 'col-md-6'">
                    <input class="form-control" type="text" v-model="item.key" placeholder="Key">
                </div>
                <div class="col-md-6">
                    <textarea rows="1" class="form-control" v-model="item.value" placeholder="Value"></textarea>
                </div>
                <div class="col-md-1" v-show="enableRowRemoval">
                    <button class="btn btn-danger" @click="removeKey(index)">&times;</button>
                </div>
            </div>
        </div>
        <p v-if="form.content.length">Rows with empty name will be removed on save</p>
        <div class="form-group">
            <button class="btn btn-default" @click="add">Add row</button>
            <button class="btn btn-default" data-toggle="popover" title="Items that will be added" :data-content="defaultKeysDescription" @click="addDefaultKeys">Add default rows</button>
            <span class="pull-right"><label>Enable row removal <input type="checkbox" v-model="enableRowRemoval"></label></span>
        </div>
        <button class="btn btn-default" @click="clearForm">Clear form</button>
        <button class="btn btn-default" v-if="clearedForm" @click="undoClear">Undo clear</button>
        <button class="btn btn-primary" @click="submit" :disabled="formIsDisabled">Save {{ form.name }}</button>
    </div>
</template>
<script>
    export default {
        props: ['item'],
        data() {
            return {
                form: {
                    name: '',
                    type: '',
                    content: []
                },
                type: '',
                enableRowRemoval: false,
                clearedForm: false,
                oldFormData: null,
                types: ['Website']
            }
        },
        computed: {
            defaultKeysDescription() {
                if(this.missingDefaultKeys.length) {
                    return this.missingDefaultKeys.map(key => key.key).join('<br>')
                }
                return "No default rows missing"
            },
            defaultKeys() {
                return [
                    {key: 'Description', value: ''},
                    {key: 'URL', value: ''},
                    {key: 'DNS', value: ''},
                    {key: 'Server Host', value: ''},
                    {key: 'Domain Registrar', value: ''},
                    {key: 'Managed by', value: ''},
                    {key: 'Server Language(s)', value: ''},
                    {key: 'Server Language Version', value: ''}
                ]
            },
            missingDefaultKeys() {
                const alreadyAddedKeys = this.form.content.map(row => row.key)
                return this.defaultKeys.filter(key => alreadyAddedKeys.indexOf(key.key) == -1)
            },
            formIsDisabled() {
                return this.form.name.trim() == "" || (this.form.type.trim() == "" && this.type.trim() == "");
            }
        },
        mounted() {
            this.$parent.$on('setitem', this.setItem)
            if(this.item) {
                this.setItem(this.item);
            }
        },
        methods: {
            undoClear() {
                Object.assign(this.$data, this.oldData);
                this.clearedForm = false;
            },
            setItem(item) {
                this.form = item;
                if(this.types.indexOf(item.type) !== -1) {
                    this.type = item.type;
                } else {
                    this.type = 'custom';
                }
            },
            add() {
                this.form.content.push({key: '', value: ''})
            },
            addDefaultKeys() {
                this.missingDefaultKeys.forEach(key => this.form.content.push(key))
            },
            removeKey(index) {
                this.form.content.splice(index, 1)
            },
            submit() {
                if(this.type !== '' && this.form.type == '') {
                    this.form.type = this.type
                }
                axios.post(this.form.id ? `/items/${this.form.id}/save` : '/items/save', this.form).then(response => {
                    this.clearForm();
                    this.clearedForm = false;
                    this.$emit('saveditem', response.data.item)
                })
            },
            clearForm() {
                const data = Object.assign({}, this.$data);
                Object.assign(this.$data, this.$options.data());
                this.clearedForm = true;
                this.oldFormData = data;
            }
        }
    }

</script>
