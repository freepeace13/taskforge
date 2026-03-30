import type { PageProps } from '@inertiajs/core';
import { Head, Link, useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import {
    ProjectFormFields,
    ProjectFormFooter,
    ProjectFormSurface,
    ProjectPageHeader,
    projectSecondaryLinkClass,
    type ProjectAttributes,
} from '@/features/projects';
import FlashMessages from '@/components/FlashMessages';
import { projectRouteParam } from '@/utils/routeBindings';

type ProjectsEditProps = PageProps & {
    organization: { slug: string; name: string };
    project: ProjectAttributes;
};

export default function ProjectsEdit({ organization, project }: ProjectsEditProps) {
    const form = useForm({
        name: project.name,
        description: project.description ?? '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.patch(route('projects.update', { org: organization.slug, project: projectRouteParam(project) }));
    };

    return (
        <>
            <Head title={`Edit — ${project.name}`} />

            <FlashMessages />

            <ProjectPageHeader organizationName={organization.name} title="Edit project" subtitle={project.name} />

            <form onSubmit={submit}>
                <ProjectFormSurface>
                    <ProjectFormFields
                        nameId="edit-project-name"
                        descriptionId="edit-project-description"
                        name={form.data.name}
                        description={form.data.description}
                        errors={form.errors}
                        onNameChange={(value) => form.setData('name', value)}
                        onDescriptionChange={(value) => form.setData('description', value)}
                    />

                    <ProjectFormFooter
                        submitLabel="Save changes"
                        processingLabel="Saving…"
                        processing={form.processing}
                        cancel={
                            <Link href={route('projects.show', { org: organization.slug, project: projectRouteParam(project) })} className={projectSecondaryLinkClass}>
                                Cancel
                            </Link>
                        }
                    />
                </ProjectFormSurface>
            </form>
        </>
    );
}
