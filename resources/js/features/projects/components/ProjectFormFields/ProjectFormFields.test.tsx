import { render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';
import ProjectFormFields from './ProjectFormFields';

describe('ProjectFormFields', () => {
    it('renders labels and forwards values', () => {
        const onName = vi.fn();
        const onDesc = vi.fn();

        render(
            <ProjectFormFields
                nameId="n"
                descriptionId="d"
                name="Alpha"
                description="Beta"
                errors={{}}
                onNameChange={onName}
                onDescriptionChange={onDesc}
            />,
        );

        expect(screen.getByLabelText('Project name')).toHaveValue('Alpha');
        expect(screen.getByLabelText('Description')).toHaveValue('Beta');
    });

    it('shows validation errors', () => {
        render(
            <ProjectFormFields
                nameId="n"
                descriptionId="d"
                name=""
                description=""
                errors={{ name: 'Required', description: 'Too long' }}
                onNameChange={vi.fn()}
                onDescriptionChange={vi.fn()}
            />,
        );

        expect(screen.getByText('Required')).toBeInTheDocument();
        expect(screen.getByText('Too long')).toBeInTheDocument();
    });
});
