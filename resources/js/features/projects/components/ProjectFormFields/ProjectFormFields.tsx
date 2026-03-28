const inputClassName =
    'mt-2 w-full rounded-2xl border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-950';

const labelClassName = 'block text-sm font-semibold text-gray-900 dark:text-gray-100';

const errorClassName = 'mt-1 text-sm text-red-600 dark:text-red-400';

type ProjectFormFieldsProps = {
    nameId: string;
    descriptionId: string;
    name: string;
    description: string;
    errors: {
        name?: string;
        description?: string;
    };
    onNameChange: (value: string) => void;
    onDescriptionChange: (value: string) => void;
};

export default function ProjectFormFields({
    nameId,
    descriptionId,
    name,
    description,
    errors,
    onNameChange,
    onDescriptionChange,
}: ProjectFormFieldsProps) {
    return (
        <>
            <div>
                <label htmlFor={nameId} className={labelClassName}>
                    Project name
                </label>
                <input
                    id={nameId}
                    className={inputClassName}
                    value={name}
                    onChange={(e) => onNameChange(e.target.value)}
                    required
                />
                {errors.name ? <p className={errorClassName}>{errors.name}</p> : null}
            </div>

            <div className="mt-5">
                <label htmlFor={descriptionId} className={labelClassName}>
                    Description
                </label>
                <textarea
                    id={descriptionId}
                    className={inputClassName}
                    rows={4}
                    value={description}
                    onChange={(e) => onDescriptionChange(e.target.value)}
                />
                {errors.description ? <p className={errorClassName}>{errors.description}</p> : null}
            </div>
        </>
    );
}
