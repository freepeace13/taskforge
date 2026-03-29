import type { PageProps } from '@inertiajs/core';
import { Head, Link, useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import {
    ProjectFormFields,
    ProjectFormFooter,
    ProjectFormSurface,
    ProjectPageHeader,
    projectSecondaryLinkClass,
} from '@/features/projects';
import FlashMessages from '@/components/FlashMessages';

type ProjectsCreateProps = PageProps & {
    organization: { slug: string; name: string };
};

export default function ProjectsCreate({ organization }: ProjectsCreateProps) {
    const form = useForm({
        name: '',
        description: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(route('projects.store', { org: organization.slug }));
    };

    return (
        <>
            <Head title="New project" />

            <FlashMessages />

            <ProjectPageHeader
                organizationName={organization.name}
                title="New project"
                subtitle="Create a project in this organization."
            />

            <form onSubmit={submit}>
                <ProjectFormSurface>
                    <ProjectFormFields
                        nameId="project-name"
                        descriptionId="project-description"
                        name={form.data.name}
                        description={form.data.description}
                        errors={form.errors}
                        onNameChange={(value) => form.setData('name', value)}
                        onDescriptionChange={(value) => form.setData('description', value)}
                    />

                    <ProjectFormFooter
                        submitLabel="Create project"
                        processingLabel="Saving…"
                        processing={form.processing}
                        cancel={
                            <Link href={route('projects.index', { org: organization.slug })} className={projectSecondaryLinkClass}>
                                Cancel
                            </Link>
                        }
                    />
                </ProjectFormSurface>
            </form>
        </>
    );
}
