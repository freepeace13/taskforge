import Button from '@/Components/Button';
import Modal from '@/Components/Modal';

type TaskModalProps = {
    isOpen: boolean;
    onClose: () => void;
};

export default function TaskModal({ isOpen, onClose }: TaskModalProps) {
    return (
        <Modal
            isOpen={isOpen}
            onClose={onClose}
            title="Create task"
            description="Add a task to a project."
            footer={
                <>
                    <Button
                        variant="ghost"
                        size="sm"
                        onClick={onClose}
                    >
                        Cancel
                    </Button>
                    <Button
                        size="sm"
                        onClick={onClose}
                    >
                        Create
                    </Button>
                </>
            }
        >
            <div>
                <label className="block text-sm font-semibold">Task title</label>
                <input
                    className="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-950 dark:placeholder:text-gray-500"
                    placeholder="e.g. Implement invites"
                />
            </div>

            <div>
                <label className="block text-sm font-semibold">Project</label>
                <select className="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-950">
                    <option>Core</option>
                    <option>API</option>
                    <option>Web</option>
                </select>
            </div>

            <div className="grid gap-4 sm:grid-cols-2">
                <div>
                    <label className="block text-sm font-semibold">Due date</label>
                    <input
                        type="date"
                        className="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-950"
                    />
                </div>
                <div>
                    <label className="block text-sm font-semibold">Status</label>
                    <select className="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-950">
                        <option>Backlog</option>
                        <option>In Progress</option>
                        <option>Done</option>
                    </select>
                </div>
            </div>
        </Modal>
    );
}
