import { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import Button from '@/Components/Button';
import Modal from '@/Components/Modal';

type ProjectStatus = 'Active' | 'On Hold' | 'Completed';

type ProjectRow = {
    id: number;
    name: string;
    description: string;
    owner: string;
    progressPercent: number;
    status: ProjectStatus;
    members: number;
};

const projects: ProjectRow[] = [
    {
        id: 1,
        name: 'Core Platform',
        description: 'Multi-tenant foundation & domain layer',
        owner: 'Kin Basco',
        progressPercent: 70,
        status: 'Active',
        members: 3,
    },
    {
        id: 2,
        name: 'API Refactor',
        description: 'Scoped bindings and clean architecture updates',
        owner: 'Alex',
        progressPercent: 40,
        status: 'On Hold',
        members: 2,
    },
];

function getStatusClasses(status: ProjectStatus): string {
    switch (status) {
        case 'Active':
            return 'inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800 dark:bg-green-500/15 dark:text-green-200';
        case 'On Hold':
            return 'inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800 dark:bg-yellow-500/15 dark:text-yellow-200';
        case 'Completed':
            return 'inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-200';
        default:
            return '';
    }
}

export default function ProjectsIndex() {
    const [isProjectModalOpen, setIsProjectModalOpen] = useState(false);

    const openProjectModal = () => setIsProjectModalOpen(true);
    const closeProjectModal = () => setIsProjectModalOpen(false);

    useEffect(() => {
        if (!isProjectModalOpen) {
            return;
        }

        const handleKeyDown = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                closeProjectModal();
            }
        };

        window.addEventListener('keydown', handleKeyDown);

        return () => {
            window.removeEventListener('keydown', handleKeyDown);
        };
    }, [isProjectModalOpen]);

    return (
        <>
            <Head title="Projects" />

            <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 className="text-xl font-bold tracking-tight">Projects</h1>
                    <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Manage and track organization projects.
                    </p>
                </div>

                <Button
                    size="md"
                    onClick={openProjectModal}
                >
                    + New Project
                </Button>
            </div>

            <div className="mb-5 grid gap-3 sm:grid-cols-3">
                <div className="relative sm:col-span-1">
                    <input
                        className="w-full rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900 dark:placeholder:text-gray-500"
                        placeholder="Search projects..."
                    />
                </div>

                <select className="rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900">
                    <option>All Status</option>
                    <option>Active</option>
                    <option>On Hold</option>
                    <option>Completed</option>
                </select>

                <select className="rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900">
                    <option>All Owners</option>
                    <option>Kin Basco</option>
                    <option>Mia</option>
                    <option>Alex</option>
                </select>
            </div>

            <div className="rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div className="overflow-x-auto">
                    <table className="min-w-full text-left text-sm">
                        <thead className="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-400">
                            <tr>
                                <th className="px-6 py-4 font-semibold">Project</th>
                                <th className="px-6 py-4 font-semibold">Owner</th>
                                <th className="px-6 py-4 font-semibold">Progress</th>
                                <th className="px-6 py-4 font-semibold">Status</th>
                                <th className="px-6 py-4 font-semibold">Members</th>
                                <th className="px-6 py-4 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>

                        <tbody className="divide-y divide-gray-200 dark:divide-gray-800">
                            {projects.map((project) => (
                                <tr
                                    key={project.id}
                                    className="hover:bg-gray-50 dark:hover:bg-gray-950/60"
                                >
                                    <td className="px-6 py-5">
                                        <div className="font-semibold">{project.name}</div>
                                        <div className="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {project.description}
                                        </div>
                                    </td>

                                    <td className="px-6 py-5 text-gray-600 dark:text-gray-300">
                                        {project.owner}
                                    </td>

                                    <td className="px-6 py-5">
                                        <div className="flex items-center gap-3">
                                            <div className="w-32 rounded-full bg-gray-200 dark:bg-gray-800">
                                                <div
                                                    className="h-2 rounded-full bg-brand-600"
                                                    style={{ width: `${project.progressPercent}%` }}
                                                />
                                            </div>
                                            <span className="text-xs font-semibold text-gray-600 dark:text-gray-300">
                                                {project.progressPercent}%
                                            </span>
                                        </div>
                                    </td>

                                    <td className="px-6 py-5">
                                        <span className={getStatusClasses(project.status)}>
                                            {project.status}
                                        </span>
                                    </td>

                                    <td className="px-6 py-5">
                                        <div className="flex -space-x-2">
                                            {Array.from({ length: project.members }).map((_, index) => (
                                                <div
                                                    // eslint-disable-next-line react/no-array-index-key
                                                    key={index}
                                                    className="h-8 w-8 rounded-xl bg-gray-300 dark:bg-gray-700"
                                                />
                                            ))}
                                        </div>
                                    </td>

                                    <td className="px-6 py-5 text-right">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                        >
                                            View
                                        </Button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>

            <Modal
                isOpen={isProjectModalOpen}
                onClose={closeProjectModal}
                title="Create Project"
                description="Start a new project inside your organization."
                footer={
                    <>
                        <Button
                            variant="ghost"
                            size="sm"
                            onClick={closeProjectModal}
                        >
                            Cancel
                        </Button>
                        <Button
                            size="sm"
                            onClick={closeProjectModal}
                        >
                            Create
                        </Button>
                    </>
                }
            >
                <div>
                    <label className="block text-sm font-semibold">Project Name</label>
                    <input
                        className="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-950"
                        placeholder="e.g. TaskForge v2"
                    />
                </div>

                <div>
                    <label className="block text-sm font-semibold">Description</label>
                    <textarea
                        className="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-950"
                        rows={3}
                    />
                </div>

                <div>
                    <label className="block text-sm font-semibold">Status</label>
                    <select className="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-950">
                        <option>Active</option>
                        <option>On Hold</option>
                        <option>Completed</option>
                    </select>
                </div>
            </Modal>
        </>
    );
}

