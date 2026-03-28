import type { TaskFormFields } from '@/features/tasks/types';

export type TaskFormProps = {
    data: TaskFormFields;
    errors: Partial<Record<keyof TaskFormFields | 'general', string>>;
    onChange: (field: keyof TaskFormFields, value: string) => void;
    id?: string;
};

export default function TaskForm({ data, errors, onChange, id }: TaskFormProps) {
    return (
        <div id={id} className="space-y-5">
            <div>
                <label htmlFor={`${id ?? 'task'}-title`} className="mb-1 block text-sm font-semibold text-gray-900 dark:text-gray-100">
                    Title
                </label>
                <input
                    id={`${id ?? 'task'}-title`}
                    type="text"
                    value={data.title}
                    onChange={(e) => onChange('title', e.target.value)}
                    className="w-full rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900"
                    required
                    maxLength={200}
                />
                {errors.title ? <p className="mt-1 text-sm text-red-600">{errors.title}</p> : null}
            </div>

            <div>
                <label htmlFor={`${id ?? 'task'}-description`} className="mb-1 block text-sm font-semibold text-gray-900 dark:text-gray-100">
                    Description
                </label>
                <textarea
                    id={`${id ?? 'task'}-description`}
                    value={data.description}
                    onChange={(e) => onChange('description', e.target.value)}
                    rows={4}
                    className="w-full rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900"
                />
                {errors.description ? <p className="mt-1 text-sm text-red-600">{errors.description}</p> : null}
            </div>

            <div className="grid gap-5 sm:grid-cols-2">
                <div>
                    <label htmlFor={`${id ?? 'task'}-priority`} className="mb-1 block text-sm font-semibold text-gray-900 dark:text-gray-100">
                        Priority
                    </label>
                    <select
                        id={`${id ?? 'task'}-priority`}
                        value={data.priority}
                        onChange={(e) => onChange('priority', e.target.value)}
                        className="w-full rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <option value="">—</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    {errors.priority ? <p className="mt-1 text-sm text-red-600">{errors.priority}</p> : null}
                </div>

                <div>
                    <label htmlFor={`${id ?? 'task'}-due`} className="mb-1 block text-sm font-semibold text-gray-900 dark:text-gray-100">
                        Due date
                    </label>
                    <input
                        id={`${id ?? 'task'}-due`}
                        type="date"
                        value={data.due_date}
                        onChange={(e) => onChange('due_date', e.target.value)}
                        className="w-full rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900"
                    />
                    {errors.due_date ? <p className="mt-1 text-sm text-red-600">{errors.due_date}</p> : null}
                </div>
            </div>
        </div>
    );
}
