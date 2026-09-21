import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\DepartamentoController::index
* @see app/Http/Controllers/Admin/DepartamentoController.php:14
* @route '/admin/departamentos'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/departamentos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::index
* @see app/Http/Controllers/Admin/DepartamentoController.php:14
* @route '/admin/departamentos'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::index
* @see app/Http/Controllers/Admin/DepartamentoController.php:14
* @route '/admin/departamentos'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::index
* @see app/Http/Controllers/Admin/DepartamentoController.php:14
* @route '/admin/departamentos'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::index
* @see app/Http/Controllers/Admin/DepartamentoController.php:14
* @route '/admin/departamentos'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::index
* @see app/Http/Controllers/Admin/DepartamentoController.php:14
* @route '/admin/departamentos'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::index
* @see app/Http/Controllers/Admin/DepartamentoController.php:14
* @route '/admin/departamentos'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::store
* @see app/Http/Controllers/Admin/DepartamentoController.php:35
* @route '/admin/departamentos'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/departamentos',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::store
* @see app/Http/Controllers/Admin/DepartamentoController.php:35
* @route '/admin/departamentos'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::store
* @see app/Http/Controllers/Admin/DepartamentoController.php:35
* @route '/admin/departamentos'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::store
* @see app/Http/Controllers/Admin/DepartamentoController.php:35
* @route '/admin/departamentos'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::store
* @see app/Http/Controllers/Admin/DepartamentoController.php:35
* @route '/admin/departamentos'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::update
* @see app/Http/Controllers/Admin/DepartamentoController.php:44
* @route '/admin/departamentos/{departamento}'
*/
export const update = (args: { departamento: string | number | { id: string | number } } | [departamento: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/departamentos/{departamento}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::update
* @see app/Http/Controllers/Admin/DepartamentoController.php:44
* @route '/admin/departamentos/{departamento}'
*/
update.url = (args: { departamento: string | number | { id: string | number } } | [departamento: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { departamento: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { departamento: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            departamento: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        departamento: typeof args.departamento === 'object'
        ? args.departamento.id
        : args.departamento,
    }

    return update.definition.url
            .replace('{departamento}', parsedArgs.departamento.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::update
* @see app/Http/Controllers/Admin/DepartamentoController.php:44
* @route '/admin/departamentos/{departamento}'
*/
update.put = (args: { departamento: string | number | { id: string | number } } | [departamento: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::update
* @see app/Http/Controllers/Admin/DepartamentoController.php:44
* @route '/admin/departamentos/{departamento}'
*/
update.patch = (args: { departamento: string | number | { id: string | number } } | [departamento: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::update
* @see app/Http/Controllers/Admin/DepartamentoController.php:44
* @route '/admin/departamentos/{departamento}'
*/
const updateForm = (args: { departamento: string | number | { id: string | number } } | [departamento: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::update
* @see app/Http/Controllers/Admin/DepartamentoController.php:44
* @route '/admin/departamentos/{departamento}'
*/
updateForm.put = (args: { departamento: string | number | { id: string | number } } | [departamento: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::update
* @see app/Http/Controllers/Admin/DepartamentoController.php:44
* @route '/admin/departamentos/{departamento}'
*/
updateForm.patch = (args: { departamento: string | number | { id: string | number } } | [departamento: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::destroy
* @see app/Http/Controllers/Admin/DepartamentoController.php:56
* @route '/admin/departamentos/{departamento}'
*/
export const destroy = (args: { departamento: string | number | { id: string | number } } | [departamento: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/departamentos/{departamento}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::destroy
* @see app/Http/Controllers/Admin/DepartamentoController.php:56
* @route '/admin/departamentos/{departamento}'
*/
destroy.url = (args: { departamento: string | number | { id: string | number } } | [departamento: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { departamento: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { departamento: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            departamento: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        departamento: typeof args.departamento === 'object'
        ? args.departamento.id
        : args.departamento,
    }

    return destroy.definition.url
            .replace('{departamento}', parsedArgs.departamento.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::destroy
* @see app/Http/Controllers/Admin/DepartamentoController.php:56
* @route '/admin/departamentos/{departamento}'
*/
destroy.delete = (args: { departamento: string | number | { id: string | number } } | [departamento: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::destroy
* @see app/Http/Controllers/Admin/DepartamentoController.php:56
* @route '/admin/departamentos/{departamento}'
*/
const destroyForm = (args: { departamento: string | number | { id: string | number } } | [departamento: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\DepartamentoController::destroy
* @see app/Http/Controllers/Admin/DepartamentoController.php:56
* @route '/admin/departamentos/{departamento}'
*/
destroyForm.delete = (args: { departamento: string | number | { id: string | number } } | [departamento: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const DepartamentoController = { index, store, update, destroy }

export default DepartamentoController