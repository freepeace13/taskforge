/**
 * Ziggy route params for `{project:slug}` — backend also accepts numeric id via Route::bind.
 */
export function projectRouteParam(project: { slug?: string | null; id: number }): string | number {
    if (project.slug != null && project.slug !== '') {
        return project.slug;
    }

    return project.id;
}

/**
 * Ziggy route params for `{task:key}` — backend also accepts numeric id via Route::bind.
 */
export function taskRouteParam(task: { key?: string | null; id: number }): string | number {
    if (task.key != null && task.key !== '') {
        return task.key;
    }

    return task.id;
}
