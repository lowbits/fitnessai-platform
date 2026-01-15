export const locales = {
    en: {
        welcome: {
            meta: {
                locale: 'en',
                title: 'Free Workout & Nutrition Plan Generator | fytrr',
                title_short: 'Create Free Workout & Nutrition Plan | fytrr',
                description:
                    'Create your free 7-day workout & nutrition plan as PDF. AI-powered, personalized & no signup required. Start now!',
                description_social:
                    'Free 7-day workout & nutrition plan as PDF. Generated with AI – personalized for muscle gain, fat loss or endurance.',
                theme_color: '#667eea',
                og: {
                    type: 'website',
                    site_name: 'fytrr.com',
                },
                twitter: {
                    card: 'summary_large_image',
                    site: '@fytrr',
                },
                structured_data: {
                    description:
                        'Personalized AI workout and meal plans tailored to your fitness goals, level, and available equipment.',
                },
            },
            hero: {
                title: 'Your Personalized',
                titleHighlight: 'Workout & Meal',
                titleEnd: 'Plans in 60 Seconds',
                subtitle:
                    'AI-generated fitness programs tailored to your goal, level, and equipment. Start your transformation today.',
            },
            convince: {
                research: {
                    headline: 'Based on Latest Research',
                    body: 'Frequent updates based on science ensure access to the latest training and nutrition recommendations.',
                },
                fast: {
                    headline: 'Get Plans Lightning Fast',
                    body: 'AI analyzes your data instantly, delivering personalized plans faster and more accurately than traditional methods.',
                },
                goals: {
                    headline: 'Reach Your Goals Faster',
                    body: 'Achieve your fitness goals with AI-generated workout & nutrition plans that adapt to your progress.',
                },
            },
            cta: {
                title: 'Start Your AI-Powered Workout Today!',
                subtitle:
                    'Get a complimentary AI-generated workout & nutrition plan designed specifically for you.',
            },
            features: {
                label: 'Features',
                title: 'Transform Your Fitness with AI-Powered Plans',
                subtitle:
                    'Explore cutting-edge features designed to optimize your workouts, nutrition, and overall progress.',
                plans: {
                    headline: 'Create Individual Plans',
                    body: 'Design unique workout and nutrition plans tailored to your specific goals and needs.',
                },
                shopping: {
                    headline: 'Shop with Ease',
                    body: 'Generate shopping lists automatically from your personalized nutrition plan.',
                },
                coach: {
                    headline: 'Get Instant Advice',
                    body: 'Receive real-time guidance from your AI Coach for on-the-spot support during workouts.',
                },
            },
            faq: {
                title: 'Frequently asked questions',
                contact: 'If you have anything else you want to ask,',
                contactLink: 'reach out to us',
                accuracy: {
                    question: 'How accurate is an AI-generated nutrition plan?',
                    answer: 'The accuracy of an AI-generated nutrition plan depends on the quality of data and algorithms used. With proper inputs, our AI provides highly accurate, personalized recommendations based on your individual needs, goals, and current fitness level. Our system is continuously updated with the latest research to ensure optimal results.',
                },
                dietitian: {
                    question:
                        'Will an AI-generated plan replace the need for a dietitian?',
                    answer: 'No, our AI-generated plans should complement, not replace, professional advice from licensed dietitians or healthcare providers. While our AI provides personalized recommendations and daily guidance, we always recommend consulting with healthcare professionals before making significant dietary or fitness changes, especially if you have specific health conditions.',
                },
                safety: {
                    question: 'How can I be sure the plan is safe for me?',
                    answer: 'Before starting any new fitness or nutrition program, we recommend consulting with your healthcare provider, especially if you have existing health conditions, injuries, or dietary restrictions. Our AI takes your input into account, but professional medical advice is always recommended for your safety.',
                },
                restrictions: {
                    question:
                        'Can the AI accommodate special diets or restrictions?',
                    answer: 'Yes! Our AI can accommodate special dietary needs including allergies, food preferences, vegetarian/vegan diets, religious restrictions, and more. Simply provide accurate information during the setup process. However, for complex medical conditions or severe allergies, please consult with a healthcare professional to ensure the plan is safe and appropriate for you.',
                },
            },
            footer: {
                imprint: 'Imprint',
            },
        },
        form: {
            validation: {
                genderRequired: 'Select your gender',
                ageRequired: 'Enter your age',
                heightRequired: 'Enter your height',
                weightRequired: 'Enter your weight',
                activityLevelRequired: 'Select your activity level',
                skillLevelRequired: 'Select your experience level',
                bodyGoalRequired: 'Select your goal',
                trainingPlaceRequired: 'Select your training location',
                trainingSessionsRequired: 'Choose how often you want to train',
            },
            success: {
                title: 'Almost there',
                thankYou: 'Your details have been saved.',
                emailSent: "We've just sent a verification email to:",
                whatNext: 'Here’s what happens next:',
                steps: {
                    inbox: 'Open your email inbox (and check spam if needed)',
                    verify: 'Verify your email address using the link',
                    generate: 'We’ll then prepare your personal plan',
                },
                generationTime: 'Estimated time',
                generationText:
                    'Your full {days}-day training and meal plan will be ready shortly after verification.',
                minutes: '3–5 minutes',
                didntReceive: 'Can’t find the email?',
                checkSpam: 'Check your spam folder or contact us at',
                linkValid: 'The verification link is valid for {hours}.',
                hours: '24 hours',
            },
            steps: {
                gender: {
                    label: 'Gender',
                    headline: "Let's start with the basics",
                    subline: 'This helps us personalize your plan',
                    error: 'Please select your gender',
                },
                personal: {
                    headline: 'Tell us about yourself',
                    subline:
                        'We need these details to calculate your calorie needs',
                    age: 'Age',
                    height: 'Height',
                    weight: 'Weight',
                    ageError: 'Please add your age',
                    heightError: 'Please add your height',
                    weightError: 'Please add your weight',
                },
                diet: {
                    headline: "What's your diet preference?",
                    subline: "We'll create meals that match your lifestyle",
                    label: 'Dietary Preferences',
                    placeholder: 'Select Diet Type',
                },
                activity: {
                    headline: 'How active are you?',
                    subline: 'This helps us understand your daily energy needs',
                    activityLabel: 'Activity Level',
                    activityHint: "What's your activity on an average day?",
                    activityPlaceholder: 'Select Activity Level',
                    activityError: 'Please select your activity level',
                    skillLabel: 'Skill Level',
                    skillHint: 'How experienced are you in fitness?',
                    skillPlaceholder: 'Select Skill Level',
                    skillError: 'Please select your skill level',
                },
                goal: {
                    headline: "What's your main goal?",
                    subline: 'Your entire plan will be built around this',
                    label: 'Fitness Goal',
                    placeholder: 'Select Goal',
                    error: 'Please select your goal',
                },
                training: {
                    headline: 'Where do you prefer to train?',
                    subline:
                        "We'll design workouts based on your available equipment",
                    placeLabel: 'Training Location',
                    placeError: 'Please select your training place',
                    sessionsHeadline: 'How many days per week?',
                    sessionsSubline:
                        "More isn't always better - consistency is key",
                    sessionsLabel: 'Training Days',
                    sessionsError: 'Please set how often you want to train',
                    sessionsSuffix: 'times',
                    recommended: '(✨ recommended)',
                },
                final: {
                    headline: 'Almost there!',
                    subline: "We'll send your personalized plan to this email",
                    name: 'Your Name',
                    email: 'Email Address',
                    terms: 'I have taken note of the {termsLink}, {disclaimerLink} and {privacyLink} and accept them.',
                    termsLinkText: 'Terms & Conditions',
                    disclaimerLinkText: 'Disclaimer',
                    newsletter:
                        'I would like to subscribe to the free newsletter and regularly receive fitness tips, workout ideas, and exclusive content.',
                    submit: 'Generate plan',
                    submitting: 'Generating...',
                },
            },
            devHelper: 'Set Form',
        },
        emailVerification: {
            generating: {
                verified: 'Email Verified!',
                welcome: 'Welcome, {name}!',
                title: 'Generating Your Personalized Plan',
                description:
                    'This may take a few minutes. You can close this page, we will inform you when your plan is ready.',
                complete: '✅ Your plan is ready! Check your emails...',
                hasFailures:
                    "⚠️ Some items failed to generate. We'll continue with what we have.",
                generating: 'Generating your personalized plan...',
                loadingStatus: 'Loading status...',
                planLabel: 'Plan:',
                startDateLabel: 'Start Date:',
            },
            expired: {
                title: 'Verification Link Expired',
                description:
                    'This verification link has expired. Verification links are valid for 24 hours.',
                whatToDo: 'What to do:',
                steps: {
                    contact:
                        'Contact us at {email} to request a new verification email',
                    include: 'Include your email address in the message',
                },
                backHome: 'Back to Home',
            },
            invalid: {
                title: 'Invalid Verification Link',
                description:
                    'This verification link is invalid or has already been used.',
                possibleReasons: 'Possible reasons:',
                reasons: {
                    alreadyVerified: 'Your email is already verified',
                    invalidLink: 'The link is malformed or incorrect',
                    alreadyUsed: 'This link has already been used',
                },
                whatToDo: 'What to do:',
                steps: {
                    tryLogin:
                        'Try logging in - your email might already be verified',
                    contact:
                        'Contact us at {email} if you continue to have issues',
                },
                backHome: 'Back to Home',
            },
        },
        enums: {
            gender: {
                male: 'Male',
                female: 'Female',
                other: 'Other',
                prefer_not_to_say: 'Prefer not to say',
            },
            bodyGoal: {
                muscle_gain: 'Muscle Gain',
                weight_loss: 'Weight Loss',
                maintenance: 'Maintenance',
                endurance: 'Endurance',
                strength: 'Strength',
            },
            skillLevel: {
                beginner: 'Beginner',
                intermediate: 'Intermediate',
                advanced: 'Advanced',
                elite: 'Elite',
            },
            activityLevel: {
                mainly_sitting: 'Mainly Sitting',
                mainly_standing: 'Mainly Standing',
                mainly_walking: 'Mainly Walking',
                hard_working: 'Hard Physical Work',
            },
            trainingPlace: {
                gym: 'Gym',
                home: 'Home',
                outdoor: 'Outdoor',
                no_preference: 'No Preference',
            },
            dietType: {
                omnivore: 'Classic',
                vegetarian: 'Vegetarian',
                pescatarian: 'Pescatarian',
                vegan: 'Vegan',
                high_protein: 'High Protein',
                low_carb: 'Low Carb',
                ketogenic: 'Ketogenic',
                paleo: 'Paleo',
                mediterranean: 'Mediterranean',
                intermittent_fasting: 'Intermittent Fasting',
            },
        },
        workout_plan: {
            hero: {
                free_badge: '100% Free',
                weeks: 'Weeks',
                times_per_week: '× per Week',
                minutes: 'Minutes',
                level: 'Level',
                cta_button: 'Create Your Personalized Plan Now',
            },
            week_overview: {
                heading: 'Your Workout Plan in Detail',
                equipment_heading: 'Required Equipment',
                progression_heading: 'Progression',
                tips_heading: 'Tips for Maximum Success',
                sets: 'Sets',
                reps: 'Reps',
                rest: 'Rest',
            },
            faq: {
                heading: 'Frequently Asked Questions',
            },
            related: {
                heading: 'More Workout Plans',
                view_plan: 'View Plan',
            },
            cta: {
                heading: 'Ready for Your Personalized Plan?',
                description:
                    'Create your individual workout and nutrition plan now, based on your goals and requirements.',
                button: 'Create Plan',
            },
            why_it_works: {
                heading: 'Why This Plan Works',
                subheading:
                    'Science-based principles that guarantee your success',
                show_more: 'Show {count} more reasons',
                show_less: 'Show less',
            },
            common_mistakes: {
                heading: 'Avoid Common Mistakes',
                subheading:
                    'Avoid these typical beginner mistakes for better results',
                problem: 'Problem',
                consequence: 'Consequences',
                solution: 'Solution',
                example: 'Practical Example',
                show_more: 'Show {count} more mistakes',
                show_less: 'Show less',
            },
            author: {
                last_updated: 'Last updated',
                reviewed_by: 'Professionally reviewed by',
                about_content: 'About our content',
                disclosure:
                    'This workout plan was created with AI assistance and reviewed by certified professionals. All recommendations are based on current sports science and evidence-based principles.',
            },
        },
        form_panel: {
            submit: 'Continue',
        },
        legal: {
            disclaimer: {
                meta: {
                    title: 'Disclaimer & Health Information | fytrr',
                    description:
                        'Important health and safety information for using fytrr workout and nutrition plans.',
                },
                title: 'Disclaimer & Health Information',
                warning: {
                    title: 'Important Notice',
                    description:
                        'Please read these notices carefully before using the platform.',
                },
                sections: {
                    not_professional: {
                        title: 'Not a Replacement for Professional Advice',
                        subtitle:
                            'The workout and nutrition plans provided through fytrr do NOT replace consultation with qualified professionals such as doctors, nutritionists, personal trainers, or other health experts.',
                        content_1:
                            'The content is for general informational purposes only. It does not constitute medical diagnosis, treatment, or professional health advice.',
                        content_2:
                            'Always consult a doctor or qualified healthcare provider before starting any new workout or nutrition program.',
                    },
                    medical_conditions: {
                        title: 'Medical Conditions & Risks',
                        warning_title: 'Consult a doctor if you:',
                        conditions: [
                            'Have or had cardiovascular disease',
                            'Have diabetes or metabolic disorders',
                            'Have joint problems, back pain, or injuries',
                            'Are pregnant or nursing',
                            'Have or had eating disorders',
                            'Have high blood pressure or other chronic conditions',
                            'Take medications that may be affected by exercise or nutrition',
                            'Are over 40 and have not exercised in a long time',
                            'Are unsure about your health status',
                        ],
                        warning_note:
                            'Ignoring health problems can lead to serious injuries or health complications.',
                    },
                    ai_content: {
                        title: 'AI-Generated Content',
                        content_1:
                            'Workout and nutrition plans are created using Artificial Intelligence (AI). While we strive to deliver high-quality recommendations, AI systems can make mistakes or generate unsuitable suggestions.',
                        content_2:
                            'The AI does not know your complete medical history, current complaints, or individual physical limitations.',
                        content_3:
                            'Use common sense and listen to your body. If something is painful or feels wrong, STOP immediately.',
                    },
                    responsibility: {
                        title: 'Personal Responsibility',
                        content_1:
                            'You bear full responsibility for implementing the provided plans. You decide whether and how to follow the recommendations.',
                        content_2:
                            'The provider assumes no responsibility for:',
                        items: [
                            'Injuries resulting from training',
                            'Health problems from dietary changes',
                            'Allergic reactions to recommended foods',
                            'Unwanted weight changes',
                            'Worsening of existing health problems',
                            'Incorrect or incomplete AI recommendations',
                        ],
                    },
                    nutrition: {
                        title: 'Nutrition Information',
                        content:
                            'Nutrition plans are general suggestions and may not consider:',
                        items: [
                            'Food allergies or intolerances',
                            'Specific dietary requirements due to illnesses',
                            'Medication-food interactions',
                            'Individual metabolic characteristics',
                            'Cultural or religious dietary requirements',
                        ],
                        note: 'Inform yourself about suggested foods and check if they are suitable for you.',
                    },
                    training: {
                        title: 'Training Information',
                        content:
                            'Training exercises can lead to injuries if performed incorrectly.',
                        safety_title: 'Important Safety Tips:',
                        tips: [
                            'Start slowly and increase intensity gradually',
                            'Learn correct exercise execution (with trainer if needed)',
                            'Use appropriate weights - ego lifting leads to injuries',
                            'Warm up before training',
                            'Ensure adequate recovery',
                            'Stop immediately if in pain',
                            'Do not train when sick or overtired',
                        ],
                    },
                    no_guarantee: {
                        title: 'No Guarantee of Results',
                        content_1:
                            'The provider gives no guarantee or assurance for specific results (weight loss, muscle gain, performance improvement, etc.).',
                        content_2:
                            'Individual results vary greatly depending on factors such as genetics, starting condition, consistency, sleep, stress, and many other variables.',
                    },
                    liability: {
                        title: 'Limitation of Liability',
                        content_1:
                            'The provider is not liable for damages of any kind (including but not limited to direct, indirect, incidental, or consequential damages) arising from the use or inability to use the provided content.',
                        content_2: 'This applies in particular to:',
                        items: [
                            'Health damage or injuries',
                            'Worsening of existing conditions',
                            'Lack of or unexpected training results',
                            'Unwanted side effects from dietary changes',
                            'Errors in AI-generated recommendations',
                        ],
                        note: 'Statutory liability limitations remain unaffected (see Terms & Conditions).',
                    },
                    fda_disclaimer: {
                        title: 'FDA Disclaimer (United States)',
                        content_1:
                            'These statements have not been evaluated by the Food and Drug Administration (FDA).',
                        content_2:
                            'This service is not intended to diagnose, treat, cure, or prevent any disease or medical condition.',
                        content_3:
                            'The information provided is for educational and informational purposes only and should not be construed as medical advice.',
                    },

                    not_licensed: {
                        title: 'No Professional License',
                        content_1:
                            'The provider is NOT a licensed medical professional, registered dietitian, certified nutritionist, or certified personal trainer.',
                        content_2:
                            'This service does not provide professional medical advice, professional nutritional counseling, or professional fitness training.',
                        content_3:
                            'You should consult with licensed professionals before making any health, nutrition, or fitness decisions.',
                    },

                    emergency: {
                        title: 'Medical Emergency',
                        icon_warning: '🚨',
                        content_1:
                            'If you experience any of the following during exercise, STOP IMMEDIATELY and seek emergency medical care:',
                        symptoms: [
                            'Chest pain or pressure',
                            'Severe shortness of breath',
                            'Dizziness or lightheadedness',
                            'Unusual or irregular heartbeat',
                            'Severe joint or muscle pain',
                            'Nausea or vomiting',
                            'Any symptom that concerns you',
                        ],
                        content_2:
                            'Emergency numbers: 911 (US/Canada), 112 (EU), 999 (UK), or your local emergency number.',
                    },

                    jurisdiction: {
                        title: 'Jurisdiction & Applicable Law',
                        content_1:
                            'These terms and services are provided under German law. The provider is based in Germany and operates under German jurisdiction.',
                        content_2:
                            'For users outside Germany: Additional local laws, regulations, and consumer protection rights may apply in your country. It is your responsibility to ensure compliance with your local laws.',
                        content_3:
                            'By using this service from outside Germany, you acknowledge that you understand and accept that German law applies to this agreement.',
                    },

                    international_users: {
                        title: 'Notice for International Users',
                        content_1:
                            'This service is primarily designed for users in Germany and the European Union.',
                        content_2:
                            'If you are using this service from outside the EU:',
                        items: [
                            "Nutritional recommendations may not align with your country's dietary guidelines",
                            'Exercise recommendations may not comply with local fitness standards',
                            'Measurement units (metric) may differ from your local standards',
                            'Language and cultural context may be different',
                        ],
                        content_3:
                            'You are responsible for adapting any recommendations to your local context and regulations.',
                    },

                    no_medical_relationship: {
                        title: 'No Doctor-Patient or Professional Relationship',
                        content_1:
                            'Use of this service does NOT create a doctor-patient relationship, nutritionist-client relationship, or trainer-client relationship.',
                        content_2:
                            'The AI-generated recommendations are automated and not reviewed by licensed professionals.',
                        content_3:
                            'No confidentiality or professional duty of care exists between you and the provider.',
                    },

                    third_party_disclaimer: {
                        title: 'Third-Party Health Information',
                        content:
                            'This service may provide links or references to third-party health information, products, or services. The provider does not endorse, guarantee, or assume responsibility for any third-party content, recommendations, or services.',
                    },

                    assumption_of_risk: {
                        title: 'Assumption of Risk',
                        content_1:
                            'You expressly acknowledge and agree that use of this service and implementation of any recommendations is at your sole risk.',
                        content_2:
                            'You understand that physical exercise and dietary changes involve inherent risks including, but not limited to:',
                        risks: [
                            'Muscle strains, sprains, and tears',
                            'Joint injuries',
                            'Cardiovascular events',
                            'Metabolic complications',
                            'Allergic reactions',
                            'Nutritional deficiencies',
                            'Psychological distress',
                            'Death (in extreme cases)',
                        ],
                        content_3:
                            'You voluntarily assume all risks associated with using this service.',
                    },

                    indemnification: {
                        title: 'Indemnification',
                        content:
                            'You agree to indemnify, defend, and hold harmless the provider, its owners, employees, and affiliates from any claims, damages, losses, liabilities, and expenses (including legal fees) arising from your use of this service or violation of these terms.',
                    },

                    no_warranties: {
                        title: 'No Warranties',
                        content_1:
                            'This service is provided "AS IS" and "AS AVAILABLE" without warranties of any kind, either express or implied.',
                        content_2:
                            'The provider makes no warranties regarding:',
                        items: [
                            'Accuracy, reliability, or completeness of AI-generated content',
                            'Fitness for a particular purpose',
                            'Merchantability',
                            'Non-infringement',
                            'Uninterrupted or error-free service',
                            'Results or outcomes from using the service',
                        ],
                    },

                    // 11. SEVERABILITY
                    severability: {
                        title: 'Severability',
                        content:
                            'If any provision of this disclaimer is found to be unenforceable or invalid under applicable law, such unenforceability or invalidity shall not render this disclaimer unenforceable or invalid as a whole. Such provisions shall be deleted without affecting the remaining provisions.',
                    },

                    // 12. CHANGES TO DISCLAIMER
                    changes: {
                        title: 'Changes to This Disclaimer',
                        content_1:
                            'The provider reserves the right to modify this disclaimer at any time without prior notice.',
                        content_2:
                            'Your continued use of the service after changes constitutes acceptance of the modified disclaimer.',
                        content_3:
                            'It is your responsibility to review this disclaimer periodically.',
                    },
                    updates: {
                        title: 'Updates',
                        content:
                            'This disclaimer may be updated at any time. The current version is available on the website.',
                    },
                },
                confirmation: {
                    title: 'By using fytrr, you confirm that you:',
                    items: [
                        'Have read and understood these notices completely',
                        'Understand and accept the risks',
                        'Take full responsibility for your health',
                        'Will seek professional advice if in doubt',
                    ],
                },

                last_updated: 'Last updated: December 2024',
            },
            imprint: {
                meta: {
                    title: 'Imprint | fytrr',
                    description:
                        'Legal information and contact details for fytrr.',
                },
                title: 'Imprint',
                sections: {
                    company: {
                        title: 'Business Information',
                        country: 'Germany',
                    },
                    contact: {
                        title: 'Contact',
                        email: 'Email',
                    },
                    tax: {
                        title: 'Tax Identification Number',
                        description: 'VAT Identification Number (Germany)',
                    },
                    dispute: {
                        title: 'EU Online Dispute Resolution',
                        description:
                            'The European Commission provides a platform for online dispute resolution (ODR): <a href="https://ec.europa.eu/consumers/odr/" target="_blank" rel="noopener noreferrer" class="text-primary-500 hover:underline">https://ec.europa.eu/consumers/odr/</a>',
                        email_note: 'Our email address can be found above.',
                    },
                    consumer_dispute: {
                        title: 'Consumer Dispute Resolution',
                        description:
                            'We are neither willing nor obligated to participate in dispute resolution proceedings before a consumer arbitration board.',
                    },
                    disclaimer: {
                        title: 'Liability Disclaimer',
                        content: {
                            title: 'Liability for Content',
                            description:
                                'The content of our pages has been created with the utmost care. However, we cannot guarantee the accuracy, completeness, or timeliness of the content. As a service provider, we are responsible for our own content on these pages in accordance with general laws. However, as a service provider, we are not obligated to monitor transmitted or stored third-party information or to investigate circumstances that indicate illegal activity. This disclaimer applies to the extent permitted by applicable law, including US federal and state consumer protection laws.',
                        },
                        links: {
                            title: 'Liability for Links',
                            description:
                                'Our website contains links to external third-party websites over whose content we have no control. Therefore, we cannot accept any liability for this third-party content. The respective provider or operator of the linked pages is always responsible for the content of the linked pages. To the extent permitted under applicable US law, we disclaim all liability for damages arising from the use of third-party content.',
                        },
                        copyright: {
                            title: 'Copyright',
                            description:
                                'The content and works created by the site operators on these pages are subject to German and international copyright law. The reproduction, editing, distribution, and any kind of use beyond the limits of copyright require the written consent of the respective author or creator. This applies to all jurisdictions where fytrr operates, including the United States.',
                        },
                    },
                },
            },
            data_privacy: {
                meta: {
                    title: 'Privacy Policy | fytrr',
                    description:
                        'Information about how we collect, use, and protect your personal data.',
                },
                title: 'Privacy Policy',
                sections: {
                    overview: {
                        title: '1. Privacy at a Glance',
                        general: {
                            title: 'General Information',
                            description:
                                'The following information provides a simple overview of what happens to your personal data when you visit this website. Personal data is any data that can be used to personally identify you.',
                        },
                        data_collection: {
                            title: 'Data Collection on This Website',
                            who_responsible: 'Who is responsible for data collection on this website?',
                            who_answer:
                                'Data processing on this website is carried out by the website operator. Contact details can be found in the imprint of this website.',
                            how_collect: 'How do we collect your data?',
                            how_answer:
                                'Your data is collected either by you providing it to us (e.g., when creating your workout plan) or automatically by our IT systems when you visit the website (e.g., IP address).',
                            why_use: 'What do we use your data for?',
                            why_answer:
                                'Your data is used to create personalized workout and nutrition plans using AI technology and to deliver them to you via email.',
                        },
                    },
                    hosting: {
                        title: '2. Hosting',
                        provider: 'Hetzner Online GmbH',
                        description:
                            'We host our website with Hetzner Online GmbH, Industriestr. 25, 91710 Gunzenhausen, Germany.',
                        details:
                            'When you visit our website, Hetzner collects various log files including your IP addresses. For details, please refer to Hetzner\'s privacy policy: <a href="https://www.hetzner.com/legal/privacy-policy" target="_blank" rel="noopener noreferrer" class="text-primary-500 hover:underline">https://www.hetzner.com/legal/privacy-policy</a>',
                        legal_basis:
                            'The use of Hetzner is based on Art. 6 (1) lit. f GDPR. We have a legitimate interest in ensuring the most reliable presentation of our website possible. For users in the United States, this processing is conducted in accordance with applicable US data protection laws and represents our legitimate business interest.',
                    },
                    general_info: {
                        title: '3. General Information and Mandatory Disclosures',
                        data_protection: {
                            title: 'Data Protection',
                            description:
                                'The operators of this website take the protection of your personal data very seriously. We treat your personal data confidentially and in accordance with statutory data protection regulations and this privacy policy.',
                        },
                        responsible_party: {
                            title: 'Information About the Responsible Party',
                            description:
                                'The party responsible for data processing on this website is:',
                            email: 'Email',
                        },
                        storage_duration: {
                            title: 'Storage Duration',
                            description:
                                'Unless a more specific storage period has been specified in this privacy policy, your personal data will remain with us until the purpose for data processing no longer applies. If you submit a legitimate deletion request or revoke consent for data processing, your data will be deleted unless we have other legally permissible reasons for storing your personal data. This applies to users in all jurisdictions, including the United States.',
                        },
                        consent_withdrawal: {
                            title: 'Withdrawal of Your Consent to Data Processing',
                            description:
                                'Many data processing operations are only possible with your express consent. You can withdraw consent you have already given at any time. The lawfulness of data processing carried out prior to the withdrawal remains unaffected by the withdrawal.',
                        },
                        complaint_right: {
                            title: 'Right to Lodge a Complaint with the Supervisory Authority',
                            description:
                                'In the event of violations of data protection law, the affected party has the right to lodge a complaint with the competent supervisory authority. For users in the European Union and Germany, this right exists regardless of other administrative or judicial remedies. For users in the United States, you may have additional rights under federal and state privacy laws, including the California Consumer Privacy Act (CCPA) and similar state laws.',
                        },
                        data_portability: {
                            title: 'Right to Data Portability',
                            description:
                                'You have the right to have data that we process automatically based on your consent or in fulfillment of a contract handed over to you or to a third party in a common, machine-readable format. This right applies to users worldwide, including those in the United States.',
                        },
                        access_rights: {
                            title: 'Information, Deletion, and Correction',
                            description:
                                'Within the scope of applicable legal provisions, you have the right to free information about your stored personal data, its origin and recipients, and the purpose of data processing at any time, as well as a right to correction or deletion of this data. Users in California and other US states may have additional rights under applicable state privacy laws.',
                        },
                    },
                    data_collection: {
                        title: '4. Data Collection on This Website',
                        server_logs: {
                            title: 'Server Log Files',
                            description:
                                'The website provider automatically collects and stores information in server log files:',
                            items: {
                                browser: 'Browser type and browser version',
                                os: 'Operating system used',
                                referrer: 'Referrer URL',
                                hostname: 'Hostname of the accessing computer',
                                time: 'Time of the server request',
                                ip: 'IP address',
                            },
                            legal_note:
                                'This data is not merged with other data sources. Data collection is based on Art. 6 (1) lit. f GDPR. For US users, this represents our legitimate business interest in maintaining website security and functionality.',
                        },
                        email_inquiry: {
                            title: 'Inquiry by Email',
                            description:
                                'If you contact us by email, your inquiry including all resulting personal data (name, inquiry) will be stored and processed by us for the purpose of handling your request. We do not share this data without your consent.',
                        },
                        registration: {
                            title: 'Registration and Use of the Service',
                            description:
                                'When using our service to create workout and nutrition plans, we collect the following data:',
                            items: {
                                email: 'Email address (for delivering the plans)',
                                body_data: 'Body data (weight, height, age, gender)',
                                goals: 'Fitness goals and preferences',
                                health: 'Health information (as provided by you)',
                            },
                            legal_note:
                                'This data is used exclusively to create your personalized plans and is processed based on your consent (Art. 6 (1) lit. a GDPR) and for contract performance (Art. 6 (1) lit. b GDPR). For US users, this processing is also in accordance with applicable federal and state privacy laws.',
                        },
                    },
                    external_services: {
                        title: '5. External Services',
                        ai: {
                            title: 'AI Services',
                            description:
                                'To create personalized workout and nutrition plans, we use AI-based services from third-party providers (USA/EU). Your entered data is transmitted to these services to generate the plans. Processing is based on Art. 6 (1) lit. a GDPR (your consent) and Art. 6 (1) lit. b GDPR (contract performance). For US users, this processing complies with applicable US privacy laws.',
                            contracts:
                                'We have concluded data processing agreements (DPA) with the providers. When data is transferred to third countries outside the EU, this is done on the basis of standard contractual clauses.',
                        },
                        resend: {
                            title: 'Resend (Email Delivery)',
                            description:
                                'We use Resend to send your personalized plans via email. Your email address is transmitted to Resend for this purpose. For more information: <a href="https://resend.com/legal/privacy-policy" target="_blank" rel="noopener noreferrer" class="text-primary-500 hover:underline">https://resend.com/legal/privacy-policy</a>',
                        },
                        plausible: {
                            title: 'Plausible Analytics',
                            description:
                                'We use Plausible Analytics, a privacy-friendly analytics tool. Plausible does not use cookies and does not collect personal data. Data collection is anonymized and complies with GDPR and US privacy regulations.',
                        },
                        polar: {
                            title: 'Polar.sh (Payment Processing)',
                            description:
                                'We use Polar.sh for payment processing. During payment transactions, your payment data is transmitted directly to Polar.sh and processed there. We do not store complete payment data. The legal basis is Art. 6 (1) lit. b GDPR (contract performance) and compliance with applicable payment processing regulations in all operating jurisdictions.',
                        },
                    },
                    data_deletion: {
                        title: '6. Data Deletion',
                        description:
                            'Your data will be deleted as soon as it is no longer required for the purpose for which it was collected. You can request deletion of your data at any time by email to hello@fytrr.com. This right applies to users in all jurisdictions, including specific deletion rights under California CCPA and similar state laws.',
                    },
                    ssl: {
                        title: '7. SSL/TLS Encryption',
                        description:
                            'This website uses SSL/TLS encryption for security reasons and to protect the transmission of confidential content. You can recognize an encrypted connection by the browser address line changing from "http://" to "https://".',
                    },
                },
                last_updated: 'Last updated: January 2026',
            },
            terms: {
                meta: {
                    title: 'Terms & Conditions | fytrr',
                    description:
                        'Terms and conditions for using fytrr services.',
                },
                title: 'Terms & Conditions',
                sections: {
                    scope: {
                        title: '§ 1 Scope of Application',
                        paragraph_1:
                            '(1) These General Terms and Conditions (hereinafter "Terms") apply to all contracts for the use of the fytrr platform (hereinafter "Platform") concluded between Tobias Lobitz (hereinafter "Provider") and the user (hereinafter "Customer").',
                        paragraph_2:
                            '(2) The Platform offers a service for the automated creation of personalized workout and nutrition plans using artificial intelligence.',
                        paragraph_3:
                            '(3) Deviating conditions of the Customer are not recognized unless the Provider expressly agrees to their validity in writing.',
                    },
                    contract: {
                        title: '§ 2 Conclusion of Contract',
                        paragraph_1:
                            '(1) The contract is concluded through the use of the Platform and the request for a workout or nutrition plan.',
                        paragraph_2:
                            '(2) By using the Platform, the Customer accepts these Terms as well as the Privacy Policy.',
                        paragraph_3:
                            '(3) Contract execution and communication are conducted in German or English.',
                    },
                    services: {
                        title: '§ 3 Scope of Services',
                        paragraph_1:
                            '(1) The Provider makes available a web platform through which customers can have personalized workout and nutrition plans created.',
                        paragraph_2:
                            '(2) The plans are created based on data provided by the Customer (weight, height, age, fitness goals, etc.) using AI technology and delivered via email in PDF format.',
                        paragraph_3:
                            '(3) The Provider does not guarantee specific availability of the Platform. The Provider is entitled to temporarily take the Platform out of operation for technical or maintenance reasons.',
                        paragraph_4:
                            '(4) The Provider reserves the right to expand, modify, or discontinue services at any time.',
                    },
                    prices: {
                        title: '§ 4 Prices and Payment',
                        paragraph_1:
                            '(1) The currently valid prices are visible on the website and include applicable sales tax.',
                        paragraph_2:
                            '(2) Payment is made through the payment service provider offered on the Platform (Polar.sh).',
                        paragraph_3:
                            '(3) The Provider reserves the right to adjust prices at any time. Payments already made are not affected by price changes.',
                    },
                    subscriptions: {
                        title: '§ 4a Subscriptions',
                        paragraph_1:
                            '(1) The Provider offers use of the Platform via mobile applications (iOS and Android) through paid subscriptions. The following subscription models are available:',
                        types: {
                            monthly: 'Monthly Subscription: Monthly billing with a duration of one month',
                            yearly: 'Annual Subscription: Annual billing with a duration of twelve months',
                        },
                        paragraph_2:
                            '(2) <strong>Automatic Renewal:</strong> All subscriptions automatically renew for the respectively booked period (monthly or annually) unless the subscription is cancelled before the end of the respective period. Renewal occurs at the then-current price, unless a different arrangement applies according to paragraph 6.',
                        paragraph_3:
                            '(3) <strong>Billing Through App Stores:</strong> Subscription fees are billed exclusively through the respective app store provider (Apple App Store for iOS devices or Google Play Store for Android devices). The Customer enters into a separate payment contract with the respective app store provider. The terms and conditions and privacy policies of Apple Inc. (Apple Media Services Terms and Conditions) or Google LLC (Google Play Terms of Service) apply to payment processing.',
                        paragraph_4:
                            '(4) <strong>Cancellation and Cancellation Process:</strong> The subscription must be cancelled by the Customer through the subscription management of the respective app store:',
                        cancellation: {
                            ios: '<strong>Apple App Store (iOS):</strong> Settings → Apple ID → Subscriptions → Select fytrr → Cancel Subscription',
                            android: '<strong>Google Play Store (Android):</strong> Open Google Play Store → Menu → Subscriptions → Select fytrr → Cancel Subscription',
                            note: 'Cancellation directly through the Provider (fytrr) is not possible. Cancellations must be made at least 24 hours before the end of the current billing period to prevent automatic renewal.',
                        },
                        paragraph_5:
                            '(5) <strong>Effective Date of Cancellation:</strong> Cancellation becomes effective at the end of the current, already paid billing period. Upon cancellation during an ongoing period, the Customer retains full access to all Premium features of the Platform until the end of that period. No prorated refund is provided for unused days within the paid period.',
                        paragraph_6:
                            '(6) <strong>Price Adjustments:</strong> The Provider reserves the right to adjust subscription prices. Existing customers will be informed of price increases at least 30 days in advance. The price increase becomes effective only from the next renewal period after expiration of the notice period. The Customer has the right to cancel the subscription before the price increase takes effect according to paragraph 4. Price reductions apply immediately upon announcement.',
                        paragraph_7:
                            '(7) <strong>Premium Subscription Features:</strong> The subscription includes access to Premium features including personalized AI-generated workout and nutrition plans, progress tracking, and advanced analytics features. The specific scope of services is determined by the description in the mobile application and may change as part of updates and further development.',
                        paragraph_8:
                            '(8) <strong>Applicable Terms and Conditions of App Store Providers:</strong> In addition to these Terms, the terms and conditions of the respective app store provider apply to in-app purchases and subscriptions. In case of conflicts between these Terms and the conditions of the app store providers, the conditions of the app store providers prevail to the extent legally permissible.',
                        paragraph_9:
                            '(9) <strong>International Legal Compliance:</strong> These subscription terms have been designed taking into account the legal requirements of various jurisdictions:',
                        compliance: {
                            germany:
                                '<strong>Germany and EU:</strong> These terms comply with the requirements of the General Data Protection Regulation (GDPR), the German Civil Code (BGB), and the Act Against Unfair Competition (UWG).',
                            uk: '<strong>United Kingdom:</strong> These terms take into account the Consumer Rights Act 2015 and ensure appropriate consumer rights for customers residing in the United Kingdom.',
                            us: '<strong>United States:</strong> These terms have been drafted in compliance with consumer protection laws of relevant US states, particularly California (California Consumer Privacy Act), New York, and other jurisdictions.',
                        },
                        paragraph_10:
                            '(10) <strong>No Prorated Refunds:</strong> In case of early cancellation, non-use of services, or account suspension due to violation of these Terms, no prorated refund of already paid subscription fees will be provided. Statutory claims, particularly for reversal in case of validly exercised right of withdrawal or in case of service defects, remain unaffected.',
                        paragraph_11:
                            '(11) <strong>Right of Withdrawal for Subscriptions:</strong> The right of withdrawal under § 6 of these Terms also applies to subscriptions. Consumers can withdraw from the subscription contract within 14 days without stating reasons. The right of withdrawal expires prematurely if the Customer has expressly agreed that the Provider begins execution of the contract before expiration of the withdrawal period, and the Customer has confirmed knowledge that consent results in loss of the right of withdrawal upon commencement of contract execution. Exercise of the right of withdrawal is made to the Provider according to § 6 of these Terms; refund of payments already made must be requested through the respective app store provider.',
                    },
                    obligations: {
                        title: '§ 5 Customer Obligations',
                        paragraph_1:
                            '(1) The Customer is obligated to provide truthful information. False or misleading information may result in incorrect or unsuitable plans.',
                        paragraph_2:
                            '(2) The Customer is solely responsible for implementing the created plans and must consider their physical condition and any health restrictions.',
                        paragraph_3:
                            '(3) The Customer may not use the Platform abusively or in violation of applicable law.',
                    },
                    withdrawal: {
                        title: '§ 6 Right of Withdrawal',
                        paragraph_1:
                            '(1) Consumers have a statutory right of withdrawal.',
                        notice: {
                            title: 'Withdrawal Instructions',
                            right_title: 'Right of Withdrawal',
                            right_text:
                                'You have the right to withdraw from this contract within fourteen days without giving any reason.',
                            period:
                                'The withdrawal period is fourteen days from the date of contract conclusion.',
                            how_to:
                                'To exercise your right of withdrawal, you must inform us (Tobias Lobitz, Annastraße 7, Email: hello@fytrr.com) by means of a clear statement (e.g., by email) of your decision to withdraw from this contract.',
                            consequences_title: 'Consequences of Withdrawal',
                            consequences_text:
                                'If you withdraw from this contract, we shall reimburse you all payments we have received from you without undue delay and no later than fourteen days from the day on which we received notice of your withdrawal from this contract.',
                            expiry_title: 'Premature Expiration of Right of Withdrawal',
                            expiry_text:
                                'The right of withdrawal expires for a contract for the provision of services if the entrepreneur has fully performed the service and only began execution after the consumer gave express consent and simultaneously confirmed knowledge that the right of withdrawal is lost upon complete contract performance.',
                        },
                    },
                    liability: {
                        title: '§ 7 Liability and Warranty',
                        paragraph_1:
                            '(1) The Provider is liable according to statutory provisions for intent and gross negligence as well as for injury to life, body, or health.',
                        paragraph_2:
                            '(2) Otherwise, the Provider is only liable for breach of essential contractual obligations (cardinal obligations). In this case, liability is limited to foreseeable, contract-typical damage. To the extent permitted under US law, including state-specific liability limitations, these limitations apply to users in the United States.',
                        paragraph_3:
                            '(3) Liability under the Product Liability Act remains unaffected.',
                        paragraph_4:
                            '(4) Further liability is excluded to the extent permitted by applicable law.',
                    },
                    copyright: {
                        title: '§ 8 Copyright and Usage Rights',
                        paragraph_1:
                            '(1) All content and works created by the Provider on this Platform are subject to German and international copyright law.',
                        paragraph_2:
                            '(2) The created workout and nutrition plans are exclusively for the personal, non-commercial use of the Customer.',
                        paragraph_3:
                            '(3) Distribution, reproduction, or commercial use of the plans is not permitted without express consent of the Provider. This applies to all jurisdictions where fytrr operates, including the United States.',
                    },
                    data_protection: {
                        title: '§ 9 Data Protection',
                        paragraph_1:
                            'The Provider processes personal data of the Customer in accordance with applicable data protection regulations. Details can be found in the Privacy Policy, which is available on the website. For users in the United States, processing complies with applicable federal and state privacy laws.',
                    },
                    final: {
                        title: '§ 10 Final Provisions',
                        paragraph_1:
                            '(1) The law of the Federal Republic of Germany applies, excluding the UN Convention on Contracts for the International Sale of Goods. For users outside Germany, this choice of law applies to the extent permitted by mandatory consumer protection laws of their jurisdiction.',
                        paragraph_2:
                            '(2) If the Customer is a merchant, legal entity under public law, or special fund under public law, the exclusive place of jurisdiction for all disputes arising from contractual relationships between the Customer and the Provider is the Provider\'s place of business.',
                        paragraph_3:
                            '(3) Should individual provisions of these Terms be or become invalid, the validity of the remaining provisions remains unaffected.',
                    },
                },
                last_updated: 'Last updated: January 2026',
            },
        },
        footer: {
            description:
                'Personalized AI workout and meal plans tailored to you.',
            copyright: '© {year} fytrr.com. Alle rights reserved',
        },
        set_password: {
            meta: {
                title: 'Set Your Password - Fytrr',
            },
            title: 'Set Your Password',
            subtitle: 'Your link will automatically open the Fytrr app',
            description:
                'Your link will automatically open the Fytrr app. If you don\'t have the app installed yet, download it below.',
            tip: {
                label: '👆 Tip:',
                text: 'If the app didn\'t open automatically, click the \"Open\" button in your browser.',
            },
            no_app: 'Don\'t have the app?',
            download_prompt: 'Download Fytrr to get started',
        },
    },
    de: {
        welcome: {
            meta: {
                locale: 'de',
                title: 'Trainingsplan & Ernährungsplan kostenlos erstellen | fytrr',
                title_short:
                    'Kostenlosen Trainings- & Ernährungsplan erstellen | fytrr',
                description:
                    'Erstelle deinen kostenlosen 7-Tage Trainings- & Ernährungsplan als PDF. KI-basiert, individuell & ohne Anmeldung. Jetzt starten!',
                description_social:
                    'Kostenloser 7-Tage Trainings- & Ernährungsplan als PDF. Erstellt mit KI – individuell für Muskelaufbau, Fettabbau oder Ausdauer.',
                theme_color: '#667eea',
                og: {
                    type: 'website',
                    site_name: 'fytrr.com',
                },
                twitter: {
                    card: 'summary_large_image',
                    site: '@fytrr',
                },
                structured_data: {
                    description:
                        'Erstelle deinen kostenlosen 7-Tage Trainings- & Ernährungsplan als PDF. KI-basiert, individuell & ohne Anmeldung.',
                },
            },
            hero: {
                title: 'Trainings- & Ernährungsplan',
                titleHighlight: 'kostenlos erstellen ',
                titleEnd: '– in 60 Sekunden',
                subtitle:
                    'Erhalte deinen persönlichen 7-Tage Trainings- & Ernährungsplan als PDF – KI-basiert und individuell nach Ziel, Trainingslevel & Equipment.',
            },
            convince: {
                research: {
                    headline: 'Basiert auf neuester Forschung',
                    body: 'Häufige Updates basierend auf wissenschaftlichen Erkenntnissen garantieren Zugang zu den neuesten Trainings- und Ernährungsempfehlungen.',
                },
                fast: {
                    headline: 'Erhalte Pläne blitzschnell',
                    body: 'Die KI analysiert deine Daten sofort und liefert personalisierte Pläne schneller und genauer als traditionelle Methoden.',
                },
                goals: {
                    headline: 'Erreiche deine Ziele schneller',
                    body: 'Erreiche deine Fitnessziele mit KI-generierten Trainings- und Ernährungsplänen, die sich deinem Fortschritt anpassen.',
                },
            },
            cta: {
                title: 'Starte noch heute dein KI-gestütztes Training!',
                subtitle:
                    'Erhalte einen kostenlosen KI-generierten Trainings- und Ernährungsplan, speziell für dich entwickelt.',
            },
            features: {
                label: 'Funktionen',
                title: 'Transformiere deine Fitness mit KI-gestützten Plänen',
                subtitle:
                    'Entdecke modernste Funktionen, die deine Trainings, Ernährung und deinen gesamten Fortschritt optimieren.',
                plans: {
                    headline: 'Erstelle individuelle Pläne',
                    body: 'Entwickle einzigartige Trainings- und Ernährungspläne, die auf deine spezifischen Ziele und Bedürfnisse zugeschnitten sind.',
                },
                shopping: {
                    headline: 'Einkaufen leicht gemacht',
                    body: 'Generiere automatisch Einkaufslisten aus deinem personalisierten Ernährungsplan.',
                },
                coach: {
                    headline: 'Erhalte sofortige Beratung',
                    body: 'Erhalte Echtzeit-Anleitungen von deinem KI-Coach für Unterstützung direkt während des Trainings.',
                },
            },
            faq: {
                title: 'Häufig gestellte Fragen',
                contact: 'Wenn du weitere Fragen hast,',
                contactLink: 'kontaktiere uns',
                accuracy: {
                    question:
                        'Wie genau ist ein KI-generierter Ernährungsplan?',
                    answer: 'Die Genauigkeit eines KI-generierten Ernährungsplans hängt von der Qualität der verwendeten Daten und Algorithmen ab. Mit korrekten Eingaben liefert unsere KI hochpräzise, personalisierte Empfehlungen basierend auf deinen individuellen Bedürfnissen, Zielen und deinem aktuellen Fitnesslevel. Unser System wird kontinuierlich mit den neuesten Forschungsergebnissen aktualisiert, um optimale Ergebnisse zu gewährleisten.',
                },
                dietitian: {
                    question:
                        'Ersetzt ein KI-generierter Plan die Notwendigkeit eines Ernährungsberaters?',
                    answer: 'Nein, unsere KI-generierten Pläne sollten professionellen Rat von lizenzierten Ernährungsberatern oder medizinischen Fachkräften ergänzen, nicht ersetzen. Während unsere KI personalisierte Empfehlungen und tägliche Anleitungen bietet, empfehlen wir immer die Konsultation von medizinischen Fachleuten, bevor du bedeutende Ernährungs- oder Fitnessänderungen vornimmst, besonders wenn du spezifische Gesundheitszustände hast.',
                },
                safety: {
                    question:
                        'Wie kann ich sicher sein, dass der Plan für mich sicher ist?',
                    answer: 'Bevor du ein neues Fitness- oder Ernährungsprogramm startest, empfehlen wir die Rücksprache mit deinem Arzt, besonders wenn du bestehende Gesundheitsprobleme, Verletzungen oder Ernährungseinschränkungen hast. Unsere KI berücksichtigt deine Eingaben, aber professioneller medizinischer Rat wird immer für deine Sicherheit empfohlen.',
                },
                restrictions: {
                    question:
                        'Kann die KI spezielle Diäten oder Einschränkungen berücksichtigen?',
                    answer: 'Ja! Unsere KI kann spezielle Ernährungsbedürfnisse berücksichtigen, einschließlich Allergien, Lebensmittelpräferenzen, vegetarischer/veganer Ernährung, religiöser Einschränkungen und mehr. Gib einfach genaue Informationen während des Einrichtungsprozesses an. Bei komplexen medizinischen Zuständen oder schweren Allergien konsultiere bitte einen medizinischen Fachmann, um sicherzustellen, dass der Plan sicher und angemessen für dich ist.',
                },
            },
            footer: {
                imprint: 'Impressum',
            },
        },
        form: {
            validation: {
                genderRequired: 'Geschlecht auswählen',
                ageRequired: 'Alter angeben',
                heightRequired: 'Größe angeben',
                weightRequired: 'Gewicht angeben',
                activityLevelRequired: 'Aktivitätslevel auswählen',
                skillLevelRequired: 'Erfahrungslevel auswählen',
                bodyGoalRequired: 'Ziel auswählen',
                trainingPlaceRequired: 'Trainingsort auswählen',
                trainingSessionsRequired: 'Trainingshäufigkeit auswählen',
            },
            success: {
                title: 'Fast geschafft',
                thankYou: 'Deine Angaben sind gespeichert.',
                emailSent:
                    'Wir haben dir gerade eine Bestätigungs-E-Mail gesendet an:',
                whatNext: 'So geht es weiter:',
                steps: {
                    inbox: 'Öffne dein E-Mail-Postfach (ggf. auch den Spam-Ordner)',
                    verify: 'Bestätige deine E-Mail-Adresse über den Link',
                    generate: 'Danach erstellen wir deinen persönlichen Plan',
                },
                generationTime: 'Dauer',
                generationText:
                    'Dein kompletter {days}-Tage-Plan mit Training & Ernährung ist kurz nach der Bestätigung für dich bereit.',
                minutes: '3–5 Minuten',
                didntReceive: 'Keine E-Mail gefunden?',
                checkSpam:
                    'Bitte prüfe deinen Spam-Ordner oder kontaktiere uns unter',
                linkValid: 'Der Bestätigungslink ist {hours} gültig.',
                hours: '24 Stunden',
            },
            steps: {
                gender: {
                    label: 'Geschlecht',
                    headline: 'Basics',
                    subline:
                        'So können wir deinen Trainingsplan optimal anpassen',
                    error: 'Bitte wähle dein Geschlecht',
                },
                personal: {
                    headline: 'Über dich',
                    subline:
                        'Diese Angaben nutzen wir, um deinen Kalorienbedarf exakt zu berechnen',
                    age: 'Alter',
                    height: 'Größe',
                    weight: 'Gewicht',
                    ageError: 'Bitte gib dein Alter an',
                    heightError: 'Bitte gib deine Größe an',
                    weightError: 'Bitte gib dein Gewicht an',
                },
                diet: {
                    headline: 'Ernährung',
                    subline:
                        'Wir erstellen Mahlzeiten, die zu deinem Alltag passen',
                    label: 'Ernährungspräferenzen',
                    placeholder: 'Diät-Typ auswählen',
                },
                activity: {
                    headline: 'Aktivität',
                    subline: 'So bestimmen wir deinen täglichen Energiebedarf',
                    activityLabel: 'Aktivitätslevel',
                    activityHint:
                        'Wie aktiv bist du an einem durchschnittlichen Tag?',
                    activityPlaceholder: 'Aktivitätslevel auswählen',
                    activityError: 'Bitte wähle dein Aktivitätslevel',
                    skillLabel: 'Erfahrungslevel',
                    skillHint: 'Wie viel Trainingserfahrung hast du?',
                    skillPlaceholder: 'Erfahrungslevel auswählen',
                    skillError: 'Bitte wähle dein Erfahrungslevel',
                },
                goal: {
                    headline: 'Dein Ziel',
                    subline:
                        'Dein kompletter Trainingsplan richtet sich danach',
                    label: 'Fitnessziel',
                    placeholder: 'Ziel auswählen',
                    error: 'Bitte wähle dein Ziel',
                },
                training: {
                    headline: 'Training',
                    subline:
                        'Wir passen die Übungen an dein verfügbares Equipment an',
                    placeLabel: 'Trainingsort',
                    placeError: 'Bitte wähle deinen Trainingsort',
                    sessionsHeadline: 'Trainingstage pro Woche',
                    sessionsSubline:
                        'Mehr ist nicht immer besser – Konstanz schlägt alles',
                    sessionsLabel: 'Trainingstage',
                    sessionsError:
                        'Bitte gib an, wie oft du trainieren möchtest',
                    sessionsSuffix: 'mal',
                    recommended: '(✨ empfohlen)',
                },
                final: {
                    headline: 'Fast geschafft!',
                    subline:
                        'Wir schicken dir deinen persönlichen Plan per E-Mail',
                    name: 'Dein Name',
                    email: 'E-Mail-Adresse',
                    terms: 'Ich habe von den {termsLink}, dem {disclaimerLink} und der {privacyLink} Kenntnis genommen und erkenne diese an.',
                    termsLinkText: 'AGB',
                    disclaimerLinkText: 'Haftungsausschluss',
                    privacyLinkText: 'Datenschutzerklärung',
                    newsletter:
                        'Ich möchte den kostenlosen Newsletter abonnieren und regelmäßig Fitness-Tipps, Trainingsideen und exklusive Inhalte erhalten.',
                    submit: 'Plan generieren',
                    submitting: 'Wird generiert...',
                },
            },
            devHelper: 'Formular füllen',
        },
        emailVerification: {
            generating: {
                verified: 'E-Mail verifiziert!',
                welcome: 'Willkommen, {name}!',
                title: 'Dein personalisierter Plan wird erstellt',
                description:
                    'Dies kann einige Minuten dauern. Du kannst diese Seite schließen, wir informieren dich, wenn dein Plan fertig ist.',
                complete: '✅ Dein Plan ist fertig! Prüfe deine E-Mails...',
                hasFailures:
                    '⚠️ Einige Elemente konnten nicht generiert werden. Wir fahren mit dem fort, was wir haben.',
                generating: 'Dein personalisierter Plan wird erstellt...',
                loadingStatus: 'Status wird geladen...',
                planLabel: 'Plan:',
                startDateLabel: 'Startdatum:',
            },
            expired: {
                title: 'Bestätigungslink abgelaufen',
                description:
                    'Dieser Bestätigungslink ist abgelaufen. Bestätigungslinks sind 24 Stunden gültig.',
                whatToDo: 'Was tun:',
                steps: {
                    contact:
                        'Kontaktiere uns unter {email}, um eine neue Bestätigungs-E-Mail anzufordern',
                    include: 'Gib deine E-Mail-Adresse in der Nachricht an',
                },
                backHome: 'Zurück zur Startseite',
            },
            invalid: {
                title: 'Ungültiger Bestätigungslink',
                description:
                    'Dieser Bestätigungslink ist ungültig oder wurde bereits verwendet.',
                possibleReasons: 'Mögliche Gründe:',
                reasons: {
                    alreadyVerified: 'Deine E-Mail ist bereits verifiziert',
                    invalidLink: 'Der Link ist fehlerhaft oder falsch',
                    alreadyUsed: 'Dieser Link wurde bereits verwendet',
                },
                whatToDo: 'Was tun:',
                steps: {
                    tryLogin:
                        'Versuche dich anzumelden - deine E-Mail könnte bereits verifiziert sein',
                    contact:
                        'Kontaktiere uns unter {email}, falls du weiterhin Probleme hast',
                },
                backHome: 'Zurück zur Startseite',
            },
        },
        enums: {
            gender: {
                male: 'Männlich',
                female: 'Weiblich',
                other: 'Divers',
                prefer_not_to_say: 'Keine Angabe',
            },
            bodyGoal: {
                muscle_gain: 'Muskelaufbau',
                weight_loss: 'Gewichtsverlust',
                maintenance: 'Erhaltung',
                endurance: 'Ausdauer',
                strength: 'Kraft',
            },
            skillLevel: {
                beginner: 'Anfänger',
                intermediate: 'Fortgeschritten',
                advanced: 'Erfahren',
                elite: 'Elite',
            },
            activityLevel: {
                mainly_sitting: 'Überwiegend sitzend',
                mainly_standing: 'Überwiegend stehend',
                mainly_walking: 'Überwiegend gehend',
                hard_working: 'Körperlich anstrengend tätig',
            },
            trainingPlace: {
                gym: 'Fitnessstudio',
                home: 'Zuhause',
                outdoor: 'Draußen',
                no_preference: 'Keine Präferenz',
            },
            dietType: {
                omnivore: 'Klassisch',
                vegetarian: 'Vegetarisch',
                pescatarian: 'Pescetarisch',
                vegan: 'Vegan',
                high_protein: 'Proteinreich',
                low_carb: 'Kohlenhydratarm',
                ketogenic: 'Ketogen',
                paleo: 'Paleo',
                mediterranean: 'Mediterran',
                intermittent_fasting: 'Intervallfasten',
            },
        },
        workout_plan: {
            hero: {
                free_badge: '100% Kostenlos',
                weeks: 'Wochen',
                times_per_week: '× pro Woche',
                minutes: 'Minuten',
                level: 'Level',
                cta_button: 'Jetzt personalisierten Plan erstellen',
            },
            week_overview: {
                heading: 'Dein Trainingsplan im Detail',
                equipment_heading: 'Benötigtes Equipment',
                progression_heading: 'Progression',
                tips_heading: 'Tipps für maximalen Erfolg',
                sets: 'Sätze',
                reps: 'Wdh.',
                rest: 'Pause',
            },
            faq: {
                heading: 'Häufig gestellte Fragen',
            },
            related: {
                heading: 'Weitere Trainingspläne',
                view_plan: 'Plan ansehen',
            },
            cta: {
                heading: 'Bereit für deinen personalisierten Plan?',
                description:
                    'Erstelle jetzt deinen individuellen Trainings- und Ernährungsplan basierend auf deinen Zielen und Voraussetzungen.',
                button: 'Plan erstellen',
            },
            why_it_works: {
                heading: 'Warum dieser Plan funktioniert',
                subheading:
                    'Wissenschaftlich fundierte Prinzipien, die deinen Erfolg garantieren',
                show_more: '{count} weitere Gründe anzeigen',
                show_less: 'Weniger anzeigen',
            },
            common_mistakes: {
                heading: 'Häufige Fehler vermeiden',
                subheading:
                    'Vermeide diese typischen Anfängerfehler für bessere Ergebnisse',
                problem: 'Problem',
                consequence: 'Folgen',
                solution: 'Lösung',
                example: 'Praxis-Beispiel',
                show_more: '{count} weitere Fehler anzeigen',
                show_less: 'Weniger anzeigen',
            },
            author: {
                last_updated: 'Zuletzt aktualisiert',
                reviewed_by: 'Fachlich geprüft von',
                about_content: 'Über unsere Inhalte',
                disclosure:
                    'Dieser Trainingsplan wurde mit KI-Unterstützung erstellt und von zertifizierten Fachleuten überprüft. Alle Empfehlungen basieren auf aktueller Sportforschung und evidenzbasierten Prinzipien.',
            },
        },
        form_panel: {
            submit: 'Weiter',
        },
        legal: {
            disclaimer: {
                meta: {
                    title: 'Haftungsausschluss & Gesundheitshinweise | fytrr',
                    description:
                        'Wichtige Gesundheits- und Sicherheitsinformationen zur Nutzung von fytrr Trainings- und Ernährungsplänen.',
                },
                title: 'Haftungsausschluss & Gesundheitshinweise',
                warning: {
                    title: 'Wichtiger Hinweis',
                    description:
                        'Bitte lesen Sie diese Hinweise sorgfältig, bevor Sie die Plattform nutzen.',
                },
                sections: {
                    not_professional: {
                        title: 'Kein Ersatz für professionelle Beratung',
                        subtitle:
                            'Die über fytrr bereitgestellten Trainings- und Ernährungspläne ersetzen NICHT die Beratung durch qualifizierte Fachkräfte wie Ärzte, Ernährungsberater, Personal Trainer oder andere Gesundheitsexperten.',
                        content_1:
                            'Die Inhalte dienen ausschließlich zu allgemeinen Informationszwecken. Sie stellen keine medizinische Diagnose, Behandlung oder professionelle Gesundheitsberatung dar.',
                        content_2:
                            'Konsultieren Sie immer einen Arzt oder qualifizierten Gesundheitsdienstleister, bevor Sie mit einem neuen Trainings- oder Ernährungsprogramm beginnen.',
                    },
                    medical_conditions: {
                        title: 'Medizinische Vorerkrankungen & Risiken',
                        warning_title:
                            'Konsultieren Sie unbedingt einen Arzt, wenn Sie:',
                        conditions: [
                            'An Herz-Kreislauf-Erkrankungen leiden oder litten',
                            'Diabetes oder Stoffwechselstörungen haben',
                            'Gelenkprobleme, Rückenschmerzen oder Verletzungen haben',
                            'Schwanger sind oder stillen',
                            'Essstörungen haben oder hatten',
                            'Bluthochdruck oder andere chronische Erkrankungen haben',
                            'Medikamente einnehmen, die durch Sport oder Ernährung beeinflusst werden können',
                            'Über 40 Jahre alt sind und lange nicht trainiert haben',
                            'Sich unsicher über Ihren Gesundheitszustand sind',
                        ],
                        warning_note:
                            'Das Ignorieren gesundheitlicher Probleme kann zu schweren Verletzungen oder gesundheitlichen Komplikationen führen.',
                    },
                    ai_content: {
                        title: 'KI-generierte Inhalte',
                        content_1:
                            'Die Trainings- und Ernährungspläne werden mittels Künstlicher Intelligenz (KI) erstellt. Obwohl wir uns bemühen, qualitativ hochwertige Empfehlungen zu liefern, können KI-Systeme Fehler machen oder ungeeignete Vorschläge generieren.',
                        content_2:
                            'Die KI kennt nicht Ihre vollständige medizinische Geschichte, aktuelle Beschwerden oder individuellen körperlichen Einschränkungen.',
                        content_3:
                            'Nutzen Sie Ihren gesunden Menschenverstand und hören Sie auf Ihren Körper. Wenn etwas schmerzhaft ist oder sich falsch anfühlt, STOPPEN Sie sofort.',
                    },
                    responsibility: {
                        title: 'Eigenverantwortung',
                        content_1:
                            'Sie tragen die volle Verantwortung für die Umsetzung der bereitgestellten Pläne. Sie entscheiden selbst, ob und wie Sie die Empfehlungen befolgen.',
                        content_2:
                            'Der Anbieter übernimmt keine Verantwortung für:',
                        items: [
                            'Verletzungen, die durch Training entstehen',
                            'Gesundheitliche Probleme durch Ernährungsumstellungen',
                            'Allergische Reaktionen auf empfohlene Lebensmittel',
                            'Unerwünschte Gewichtsveränderungen',
                            'Verschlechterung bestehender Gesundheitsprobleme',
                            'Fehlerhafte oder unvollständige KI-Empfehlungen',
                        ],
                    },
                    nutrition: {
                        title: 'Ernährungshinweise',
                        content:
                            'Ernährungspläne sind allgemeine Vorschläge und berücksichtigen möglicherweise nicht:',
                        items: [
                            'Lebensmittelallergien oder Unverträglichkeiten',
                            'Spezifische diätetische Anforderungen aufgrund von Erkrankungen',
                            'Medikamenten-Nahrungsmittel-Interaktionen',
                            'Individuelle Stoffwechselbesonderheiten',
                            'Kulturelle oder religiöse Ernährungsanforderungen',
                        ],
                        note: 'Informieren Sie sich über die vorgeschlagenen Lebensmittel und prüfen Sie, ob diese für Sie geeignet sind.',
                    },
                    training: {
                        title: 'Trainingshinweise',
                        content:
                            'Trainingsübungen können bei falscher Ausführung zu Verletzungen führen.',
                        safety_title: 'Wichtige Sicherheitshinweise:',
                        tips: [
                            'Beginnen Sie langsam und steigern Sie die Intensität schrittweise',
                            'Lernen Sie die korrekte Übungsausführung (ggf. mit Trainer)',
                            'Verwenden Sie angemessene Gewichte - Ego-Lifting führt zu Verletzungen',
                            'Wärmen Sie sich vor dem Training auf',
                            'Achten Sie auf ausreichende Erholung',
                            'Stoppen Sie bei Schmerzen sofort',
                            'Trainieren Sie nicht krank oder übermüdet',
                        ],
                    },
                    no_guarantee: {
                        title: 'Keine Garantie für Ergebnisse',
                        content_1:
                            'Der Anbieter gibt keine Garantie oder Zusicherung für bestimmte Ergebnisse (Gewichtsverlust, Muskelaufbau, Leistungssteigerung etc.).',
                        content_2:
                            'Individuelle Ergebnisse variieren stark abhängig von Faktoren wie Genetik, Ausgangszustand, Konsistenz, Schlaf, Stress und vielen weiteren Variablen.',
                    },
                    liability: {
                        title: 'Haftungsausschluss',
                        content_1:
                            'Der Anbieter haftet nicht für Schäden jeglicher Art (einschließlich, aber nicht beschränkt auf direkte, indirekte, zufällige oder Folgeschäden), die aus der Nutzung oder der Unfähigkeit zur Nutzung der bereitgestellten Inhalte entstehen.',
                        content_2: 'Dies gilt insbesondere für:',
                        items: [
                            'Gesundheitliche Schäden oder Verletzungen',
                            'Verschlechterung bestehender Erkrankungen',
                            'Ausbleibende oder nicht erwartete Trainingsergebnisse',
                            'Unerwünschte Nebenwirkungen von Ernährungsumstellungen',
                            'Fehler in den KI-generierten Empfehlungen',
                        ],
                        note: 'Die gesetzlichen Haftungsbeschränkungen bleiben unberührt (siehe AGB).',
                    },
                    fda_disclaimer: {
                        title: 'FDA-Hinweis (USA)',
                        content_1:
                            'Diese Aussagen wurden nicht von der Food and Drug Administration (FDA) bewertet.',
                        content_2:
                            'Dieser Service ist nicht dazu bestimmt, Krankheiten oder medizinische Zustände zu diagnostizieren, zu behandeln, zu heilen oder zu verhindern.',
                        content_3:
                            'Die bereitgestellten Informationen dienen ausschließlich Bildungs- und Informationszwecken und sind nicht als medizinischer Rat zu verstehen.',
                    },

                    // 2. KEINE BERUFLICHE LIZENZ
                    not_licensed: {
                        title: 'Keine berufliche Qualifikation',
                        content_1:
                            'Der Anbieter ist KEIN zugelassener Arzt, Ernährungsberater, zertifizierter Trainer oder sonstiger Gesundheitsexperte.',
                        content_2:
                            'Dieser Service bietet keine professionelle medizinische Beratung, professionelle Ernährungsberatung oder professionelles Fitnesstraining.',
                        content_3:
                            'Sie sollten vor gesundheitlichen, ernährungsbezogenen oder fitnessbezogenen Entscheidungen lizenzierte Fachkräfte konsultieren.',
                    },

                    // 3. NOTFALL-WARNUNG
                    emergency: {
                        title: 'Medizinischer Notfall',
                        icon_warning: '🚨',
                        content_1:
                            'Wenn Sie während des Trainings eines der folgenden Symptome verspüren, STOPPEN Sie SOFORT und suchen Sie notärztliche Hilfe:',
                        symptoms: [
                            'Brustschmerzen oder Druckgefühl',
                            'Schwere Atemnot',
                            'Schwindel oder Benommenheit',
                            'Ungewöhnlicher oder unregelmäßiger Herzschlag',
                            'Starke Gelenk- oder Muskelschmerzen',
                            'Übelkeit oder Erbrechen',
                            'Jedes Symptom, das Sie beunruhigt',
                        ],
                        content_2:
                            'Notrufnummern: 112 (EU/Deutschland), 911 (USA/Kanada), 999 (UK) oder Ihre lokale Notrufnummer.',
                    },

                    // 4. GERICHTSSTAND & ANWENDBARES RECHT
                    jurisdiction: {
                        title: 'Gerichtsstand & Anwendbares Recht',
                        content_1:
                            'Diese Bedingungen und Leistungen werden nach deutschem Recht erbracht. Der Anbieter hat seinen Sitz in Deutschland und unterliegt der deutschen Gerichtsbarkeit.',
                        content_2:
                            'Für Nutzer außerhalb Deutschlands: Zusätzliche lokale Gesetze, Vorschriften und Verbraucherschutzrechte können in Ihrem Land gelten. Sie sind dafür verantwortlich, die Einhaltung Ihrer lokalen Gesetze sicherzustellen.',
                        content_3:
                            'Durch die Nutzung dieses Services außerhalb Deutschlands bestätigen Sie, dass Sie verstehen und akzeptieren, dass deutsches Recht für diese Vereinbarung gilt.',
                    },

                    // 5. HINWEIS FÜR INTERNATIONALE NUTZER
                    international_users: {
                        title: 'Hinweis für internationale Nutzer',
                        content_1:
                            'Dieser Service ist primär für Nutzer in Deutschland und der Europäischen Union konzipiert.',
                        content_2:
                            'Wenn Sie diesen Service außerhalb der EU nutzen:',
                        items: [
                            'Ernährungsempfehlungen entsprechen möglicherweise nicht den Ernährungsrichtlinien Ihres Landes',
                            'Trainingsempfehlungen entsprechen möglicherweise nicht lokalen Fitnessstandards',
                            'Maßeinheiten (metrisch) können von Ihren lokalen Standards abweichen',
                            'Sprache und kultureller Kontext können unterschiedlich sein',
                        ],
                        content_3:
                            'Sie sind dafür verantwortlich, Empfehlungen an Ihren lokalen Kontext und Ihre Vorschriften anzupassen.',
                    },

                    // 6. KEINE MEDIZINISCHE BEZIEHUNG
                    no_medical_relationship: {
                        title: 'Keine Arzt-Patienten- oder professionelle Beziehung',
                        content_1:
                            'Die Nutzung dieses Services begründet KEINE Arzt-Patienten-Beziehung, Ernährungsberater-Klienten-Beziehung oder Trainer-Klienten-Beziehung.',
                        content_2:
                            'Die KI-generierten Empfehlungen sind automatisiert und werden nicht von lizenzierten Fachkräften überprüft.',
                        content_3:
                            'Es besteht keine Vertraulichkeit oder professionelle Sorgfaltspflicht zwischen Ihnen und dem Anbieter.',
                    },

                    // 7. DRITTANBIETER-GESUNDHEITSINFORMATIONEN
                    third_party_disclaimer: {
                        title: 'Gesundheitsinformationen von Drittanbietern',
                        content:
                            'Dieser Service kann Links oder Verweise auf Gesundheitsinformationen, Produkte oder Dienstleistungen von Drittanbietern bereitstellen. Der Anbieter befürwortet, garantiert oder übernimmt keine Verantwortung für Inhalte, Empfehlungen oder Dienstleistungen von Drittanbietern.',
                    },

                    // 8. RISIKOÜBERNAHME
                    assumption_of_risk: {
                        title: 'Risikoübernahme',
                        content_1:
                            'Sie erkennen ausdrücklich an und stimmen zu, dass die Nutzung dieses Services und die Umsetzung von Empfehlungen auf Ihr alleiniges Risiko erfolgt.',
                        content_2:
                            'Sie verstehen, dass körperliche Bewegung und Ernährungsumstellungen inhärente Risiken beinhalten, einschließlich, aber nicht beschränkt auf:',
                        risks: [
                            'Muskelzerrungen, Verstauchungen und Risse',
                            'Gelenkverletzungen',
                            'Kardiovaskuläre Ereignisse',
                            'Stoffwechselkomplikationen',
                            'Allergische Reaktionen',
                            'Nährstoffmängel',
                            'Psychische Belastungen',
                            'Tod (in extremen Fällen)',
                        ],
                        content_3:
                            'Sie übernehmen freiwillig alle Risiken, die mit der Nutzung dieses Services verbunden sind.',
                    },

                    // 9. FREISTELLUNG
                    indemnification: {
                        title: 'Freistellung',
                        content:
                            'Sie verpflichten sich, den Anbieter, seine Eigentümer, Mitarbeiter und verbundenen Unternehmen von allen Ansprüchen, Schäden, Verlusten, Verbindlichkeiten und Kosten (einschließlich Anwaltskosten) freizustellen, die aus Ihrer Nutzung dieses Services oder der Verletzung dieser Bedingungen entstehen.',
                    },

                    // 10. KEINE GEWÄHRLEISTUNG
                    no_warranties: {
                        title: 'Keine Gewährleistung',
                        content_1:
                            'Dieser Service wird "WIE BESEHEN" und "WIE VERFÜGBAR" ohne Gewährleistungen jeglicher Art bereitgestellt, weder ausdrücklich noch stillschweigend.',
                        content_2:
                            'Der Anbieter übernimmt keine Gewährleistung für:',
                        items: [
                            'Richtigkeit, Zuverlässigkeit oder Vollständigkeit KI-generierter Inhalte',
                            'Eignung für einen bestimmten Zweck',
                            'Marktgängigkeit',
                            'Nichtverletzung von Rechten',
                            'Unterbrechungsfreien oder fehlerfreien Service',
                            'Ergebnisse oder Resultate aus der Nutzung des Services',
                        ],
                    },

                    // 11. SALVATORISCHE KLAUSEL
                    severability: {
                        title: 'Salvatorische Klausel',
                        content:
                            'Sollte eine Bestimmung dieses Haftungsausschlusses nach geltendem Recht nicht durchsetzbar oder ungültig sein, wird dadurch die Durchsetzbarkeit oder Gültigkeit des Haftungsausschlusses als Ganzes nicht berührt. Solche Bestimmungen werden gestrichen, ohne die verbleibenden Bestimmungen zu beeinträchtigen.',
                    },
                    updates: {
                        title: 'Aktualisierungen',
                        content:
                            'Dieser Haftungsausschluss kann jederzeit aktualisiert werden. Die jeweils aktuelle Version ist auf der Website einsehbar.',
                    },
                },
                confirmation: {
                    title: 'Durch die Nutzung von fytrr bestätigen Sie, dass Sie:',
                    items: [
                        'Diese Hinweise vollständig gelesen und verstanden haben',
                        'Die Risiken verstehen und akzeptieren',
                        'Die volle Verantwortung für Ihre Gesundheit übernehmen',
                        'Im Zweifelsfall professionelle Beratung einholen werden',
                    ],
                },

                changes: {
                    title: 'Änderungen dieses Haftungsausschlusses',
                    content_1:
                        'Der Anbieter behält sich das Recht vor, diesen Haftungsausschluss jederzeit ohne vorherige Ankündigung zu ändern.',
                    content_2:
                        'Ihre fortgesetzte Nutzung des Services nach Änderungen gilt als Annahme des geänderten Haftungsausschlusses.',
                    content_3:
                        'Es liegt in Ihrer Verantwortung, diesen Haftungsausschluss regelmäßig zu überprüfen.',
                },
                last_updated: 'Stand: Dezember 2024',
            },
            imprint: {
                meta: {
                    title: 'Impressum | fytrr',
                    description:
                        'Rechtliche Informationen und Kontaktdaten von fytrr.',
                },
                title: 'Impressum',
                sections: {
                    company: {
                        title: 'Angaben gemäß § 5 TMG',
                        country: 'Deutschland',
                    },
                    contact: {
                        title: 'Kontakt',
                        email: 'E-Mail',
                    },
                    tax: {
                        title: 'Umsatzsteuer-ID',
                        description:
                            'Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz',
                    },
                    dispute: {
                        title: 'EU-Streitschlichtung',
                        description:
                            'Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit: <a href="https://ec.europa.eu/consumers/odr/" target="_blank" rel="noopener noreferrer" class="text-primary-500 hover:underline">https://ec.europa.eu/consumers/odr/</a>',
                        email_note:
                            'Unsere E-Mail-Adresse finden Sie oben im Impressum.',
                    },
                    consumer_dispute: {
                        title: 'Verbraucherstreitbeilegung',
                        description:
                            'Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.',
                    },
                    disclaimer: {
                        title: 'Haftungsausschluss',
                        content: {
                            title: 'Haftung für Inhalte',
                            description:
                                'Die Inhalte unserer Seiten wurden mit größter Sorgfalt erstellt. Für die Richtigkeit, Vollständigkeit und Aktualität der Inhalte können wir jedoch keine Gewähr übernehmen. Als Diensteanbieter sind wir gemäß § 7 Abs.1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen.',
                        },
                        links: {
                            title: 'Haftung für Links',
                            description:
                                'Unser Angebot enthält Links zu externen Websites Dritter, auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich.',
                        },
                        copyright: {
                            title: 'Urheberrecht',
                            description:
                                'Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers.',
                        },
                    },
                },
            },
            data_privacy: {
                meta: {
                    title: 'Datenschutzerklärung | fytrr',
                    description:
                        'Informationen darüber, wie wir Ihre personenbezogenen Daten erheben, verwenden und schützen.',
                },
                title: 'Datenschutzerklärung',
                sections: {
                    overview: {
                        title: '1. Datenschutz auf einen Blick',
                        general: {
                            title: 'Allgemeine Hinweise',
                            description:
                                'Die folgenden Hinweise geben einen einfachen Überblick darüber, was mit Ihren personenbezogenen Daten passiert, wenn Sie diese Website besuchen. Personenbezogene Daten sind alle Daten, mit denen Sie persönlich identifiziert werden können.',
                        },
                        data_collection: {
                            title: 'Datenerfassung auf dieser Website',
                            who_responsible:
                                'Wer ist verantwortlich für die Datenerfassung auf dieser Website?',
                            who_answer:
                                'Die Datenverarbeitung auf dieser Website erfolgt durch den Websitebetreiber. Dessen Kontaktdaten können Sie dem Impressum dieser Website entnehmen.',
                            how_collect: 'Wie erfassen wir Ihre Daten?',
                            how_answer:
                                'Ihre Daten werden zum einen dadurch erhoben, dass Sie uns diese mitteilen (z.B. bei der Erstellung Ihres Trainingsplans). Andere Daten werden automatisch beim Besuch der Website durch unsere IT-Systeme erfasst (z.B. IP-Adresse).',
                            why_use: 'Wofür nutzen wir Ihre Daten?',
                            why_answer:
                                'Ihre Daten werden verwendet, um personalisierte Trainings- und Ernährungspläne mittels KI-Technologie zu erstellen und Ihnen diese per E-Mail zuzustellen.',
                        },
                    },
                    hosting: {
                        title: '2. Hosting',
                        provider: 'Hetzner Online GmbH',
                        description:
                            'Wir hosten unsere Website bei Hetzner Online GmbH, Industriestr. 25, 91710 Gunzenhausen, Deutschland.',
                        details:
                            'Wenn Sie unsere Website besuchen, erfasst Hetzner verschiedene Logfiles inklusive Ihrer IP-Adressen. Details entnehmen Sie der Datenschutzerklärung von Hetzner: <a href="https://www.hetzner.com/de/rechtliches/datenschutz" target="_blank" rel="noopener noreferrer" class="text-primary-500 hover:underline">https://www.hetzner.com/de/rechtliches/datenschutz</a>',
                        legal_basis:
                            'Die Verwendung von Hetzner erfolgt auf Grundlage von Art. 6 Abs. 1 lit. f DSGVO. Wir haben ein berechtigtes Interesse an einer möglichst zuverlässigen Darstellung unserer Website.',
                    },
                    general_info: {
                        title: '3. Allgemeine Hinweise und Pflichtinformationen',
                        data_protection: {
                            title: 'Datenschutz',
                            description:
                                'Die Betreiber dieser Seiten nehmen den Schutz Ihrer persönlichen Daten sehr ernst. Wir behandeln Ihre personenbezogenen Daten vertraulich und entsprechend der gesetzlichen Datenschutzvorschriften sowie dieser Datenschutzerklärung.',
                        },
                        responsible_party: {
                            title: 'Hinweis zur verantwortlichen Stelle',
                            description:
                                'Die verantwortliche Stelle für die Datenverarbeitung auf dieser Website ist:',
                            email: 'E-Mail',
                        },
                        storage_duration: {
                            title: 'Speicherdauer',
                            description:
                                'Soweit innerhalb dieser Datenschutzerklärung keine speziellere Speicherdauer genannt wurde, verbleiben Ihre personenbezogenen Daten bei uns, bis der Zweck für die Datenverarbeitung entfällt. Wenn Sie ein berechtigtes Löschersuchen geltend machen oder eine Einwilligung zur Datenverarbeitung widerrufen, werden Ihre Daten gelöscht.',
                        },
                        consent_withdrawal: {
                            title: 'Widerruf Ihrer Einwilligung zur Datenverarbeitung',
                            description:
                                'Viele Datenverarbeitungsvorgänge sind nur mit Ihrer ausdrücklichen Einwilligung möglich. Sie können eine bereits erteilte Einwilligung jederzeit widerrufen. Die Rechtmäßigkeit der bis zum Widerruf erfolgten Datenverarbeitung bleibt vom Widerruf unberührt.',
                        },
                        complaint_right: {
                            title: 'Beschwerderecht bei der zuständigen Aufsichtsbehörde',
                            description:
                                'Im Falle von Verstößen gegen die DSGVO steht den Betroffenen ein Beschwerderecht bei einer Aufsichtsbehörde zu. Das Beschwerderecht besteht unbeschadet anderweitiger verwaltungsrechtlicher oder gerichtlicher Rechtsbehelfe.',
                        },
                        data_portability: {
                            title: 'Recht auf Datenübertragbarkeit',
                            description:
                                'Sie haben das Recht, Daten, die wir auf Grundlage Ihrer Einwilligung oder in Erfüllung eines Vertrags automatisiert verarbeiten, an sich oder an einen Dritten in einem gängigen, maschinenlesbaren Format aushändigen zu lassen.',
                        },
                        access_rights: {
                            title: 'Auskunft, Löschung und Berichtigung',
                            description:
                                'Sie haben im Rahmen der geltenden gesetzlichen Bestimmungen jederzeit das Recht auf unentgeltliche Auskunft über Ihre gespeicherten personenbezogenen Daten, deren Herkunft und Empfänger und den Zweck der Datenverarbeitung und ggf. ein Recht auf Berichtigung oder Löschung dieser Daten.',
                        },
                    },
                    data_collection: {
                        title: '4. Datenerfassung auf dieser Website',
                        server_logs: {
                            title: 'Server-Log-Dateien',
                            description:
                                'Der Provider der Seiten erhebt und speichert automatisch Informationen in sogenannten Server-Log-Dateien:',
                            items: {
                                browser: 'Browsertyp und Browserversion',
                                os: 'Verwendetes Betriebssystem',
                                referrer: 'Referrer URL',
                                hostname: 'Hostname des zugreifenden Rechners',
                                time: 'Uhrzeit der Serveranfrage',
                                ip: 'IP-Adresse',
                            },
                            legal_note:
                                'Eine Zusammenführung dieser Daten mit anderen Datenquellen wird nicht vorgenommen. Die Erfassung dieser Daten erfolgt auf Grundlage von Art. 6 Abs. 1 lit. f DSGVO.',
                        },
                        email_inquiry: {
                            title: 'Anfrage per E-Mail',
                            description:
                                'Wenn Sie uns per E-Mail kontaktieren, wird Ihre Anfrage inklusive aller daraus hervorgehenden personenbezogenen Daten (Name, Anfrage) zum Zwecke der Bearbeitung Ihres Anliegens bei uns gespeichert und verarbeitet. Diese Daten geben wir nicht ohne Ihre Einwilligung weiter.',
                        },
                        registration: {
                            title: 'Registrierung und Nutzung des Services',
                            description:
                                'Bei der Nutzung unseres Services zur Erstellung von Trainings- und Ernährungsplänen erfassen wir folgende Daten:',
                            items: {
                                email: 'E-Mail-Adresse (zur Zusendung der Pläne)',
                                body_data: 'Körperdaten (Gewicht, Größe, Alter, Geschlecht)',
                                goals: 'Fitnessziele und Präferenzen',
                                health: 'Gesundheitsinformationen (soweit von Ihnen angegeben)',
                            },
                            legal_note:
                                'Diese Daten werden ausschließlich zur Erstellung Ihrer personalisierten Pläne verwendet und auf Grundlage Ihrer Einwilligung (Art. 6 Abs. 1 lit. a DSGVO) verarbeitet.',
                        },
                    },
                    external_services: {
                        title: '5. Externe Dienste',
                        ai: {
                            title: 'KI-Dienste',
                            description:
                                'Zur Erstellung personalisierter Trainings- und Ernährungspläne nutzen wir KI-basierte Dienste von Drittanbietern (USA/EU). Ihre eingegebenen Daten werden an diese Dienste übermittelt, um die Pläne zu generieren. Die Verarbeitung erfolgt auf Grundlage von Art. 6 Abs. 1 lit. a DSGVO (Ihre Einwilligung) und Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung).',
                            contracts:
                                'Wir haben mit den Anbietern Auftragsverarbeitungsverträge (AVV) geschlossen. Bei Übermittlung in Drittländer außerhalb der EU erfolgt dies auf Basis von Standardvertragsklauseln.',
                        },
                        resend: {
                            title: 'Resend (E-Mail-Versand)',
                            description:
                                'Wir nutzen Resend zum Versand Ihrer personalisierten Pläne per E-Mail. Dabei wird Ihre E-Mail-Adresse an Resend übermittelt. Weitere Informationen: <a href="https://resend.com/legal/privacy-policy" target="_blank" rel="noopener noreferrer" class="text-primary-500 hover:underline">https://resend.com/legal/privacy-policy</a>',
                        },
                        plausible: {
                            title: 'Plausible Analytics',
                            description:
                                'Wir nutzen Plausible Analytics, ein datenschutzfreundliches Analyse-Tool. Plausible verwendet keine Cookies und erfasst keine personenbezogenen Daten. Die Erfassung erfolgt anonymisiert.',
                        },
                        polar: {
                            title: 'Polar.sh (Zahlungsabwicklung)',
                            description:
                                'Für die Zahlungsabwicklung nutzen wir Polar.sh. Bei Zahlungsvorgängen werden Ihre Zahlungsdaten direkt an Polar.sh übermittelt und dort verarbeitet. Wir speichern keine vollständigen Zahlungsdaten. Rechtsgrundlage ist Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung).',
                        },
                    },
                    data_deletion: {
                        title: '6. Datenlöschung',
                        description:
                            'Ihre Daten werden gelöscht, sobald sie für die Erreichung des Zweckes ihrer Erhebung nicht mehr erforderlich sind. Sie können jederzeit die Löschung Ihrer Daten per E-Mail an hello@fytrr.com beantragen.',
                    },
                    ssl: {
                        title: '7. SSL- bzw. TLS-Verschlüsselung',
                        description:
                            'Diese Seite nutzt aus Sicherheitsgründen und zum Schutz der Übertragung vertraulicher Inhalte eine SSL- bzw. TLS-Verschlüsselung. Eine verschlüsselte Verbindung erkennen Sie daran, dass die Adresszeile des Browsers von "http://" auf "https://" wechselt.',
                    },
                },
                last_updated: 'Stand: Januar 2026',
            },
            terms: {
                meta: {
                    title: 'AGB | fytrr',
                    description:
                        'Allgemeine Geschäftsbedingungen für die Nutzung von fytrr-Diensten.',
                },
                title: 'Allgemeine Geschäftsbedingungen',
                sections: {
                    scope: {
                        title: '§ 1 Geltungsbereich',
                        paragraph_1:
                            '(1) Diese Allgemeinen Geschäftsbedingungen (nachfolgend "AGB") gelten für alle Verträge über die Nutzung der Plattform fytrr (nachfolgend "Plattform"), die zwischen Tobias Lobitz (nachfolgend "Anbieter") und dem Nutzer (nachfolgend "Kunde") geschlossen werden.',
                        paragraph_2:
                            '(2) Die Plattform bietet einen Service zur automatisierten Erstellung personalisierter Trainings- und Ernährungspläne mittels künstlicher Intelligenz.',
                        paragraph_3:
                            '(3) Abweichende Bedingungen des Kunden werden nicht anerkannt, es sei denn, der Anbieter stimmt ihrer Geltung ausdrücklich schriftlich zu.',
                    },
                    contract: {
                        title: '§ 2 Vertragsschluss',
                        paragraph_1:
                            '(1) Der Vertrag kommt durch die Nutzung der Plattform und die Anforderung eines Trainings- oder Ernährungsplans zustande.',
                        paragraph_2:
                            '(2) Mit der Nutzung der Plattform akzeptiert der Kunde diese AGB sowie die Datenschutzerklärung.',
                        paragraph_3:
                            '(3) Die Vertragsdurchführung und Kontaktaufnahme erfolgen in deutscher oder englischer Sprache.',
                    },
                    services: {
                        title: '§ 3 Leistungsumfang',
                        paragraph_1:
                            '(1) Der Anbieter stellt eine Web-Plattform zur Verfügung, über die Kunden personalisierte Trainings- und Ernährungspläne erstellen lassen können.',
                        paragraph_2:
                            '(2) Die Pläne werden auf Basis der vom Kunden angegebenen Daten (Gewicht, Größe, Alter, Fitnessziele etc.) mittels KI-Technologie erstellt und per E-Mail im PDF-Format zugestellt.',
                        paragraph_3:
                            '(3) Der Anbieter schuldet keine bestimmte Verfügbarkeit der Plattform. Der Anbieter ist berechtigt, die Plattform aus technischen oder wartungsbedingten Gründen vorübergehend außer Betrieb zu nehmen.',
                        paragraph_4:
                            '(4) Der Anbieter behält sich vor, die Leistungen jederzeit zu erweitern, zu verändern oder einzustellen.',
                    },
                    prices: {
                        title: '§ 4 Preise und Zahlung',
                        paragraph_1:
                            '(1) Die jeweils gültigen Preise sind auf der Website ersichtlich und verstehen sich inklusive der gesetzlichen Umsatzsteuer.',
                        paragraph_2:
                            '(2) Die Zahlung erfolgt über den auf der Plattform angebotenen Zahlungsdienstleister (Polar.sh).',
                        paragraph_3:
                            '(3) Der Anbieter behält sich vor, die Preise jederzeit anzupassen. Bereits getätigte Zahlungen sind von Preisänderungen nicht betroffen.',
                    },
                    subscriptions: {
                        title: '§ 4a Abonnements',
                        paragraph_1:
                            '(1) Der Anbieter bietet die Nutzung der Plattform über die mobilen Applikationen (iOS und Android) im Rahmen von kostenpflichtigen Abonnements an. Folgende Abonnement-Modelle stehen zur Verfügung:',
                        types: {
                            monthly: 'Monatsabonnement: Monatliche Abrechnung mit Laufzeit von einem Monat',
                            yearly: 'Jahresabonnement: Jährliche Abrechnung mit Laufzeit von zwölf Monaten',
                        },
                        paragraph_2:
                            '(2) <strong>Automatische Verlängerung:</strong> Alle Abonnements verlängern sich automatisch um die jeweils gebuchte Laufzeit (monatlich bzw. jährlich), sofern das Abonnement nicht vor Ablauf der jeweiligen Periode gekündigt wird. Die Verlängerung erfolgt zum dann gültigen Preis, sofern keine abweichende Regelung nach Absatz 6 greift.',
                        paragraph_3:
                            '(3) <strong>Abrechnung über App Stores:</strong> Die Abrechnung der Abonnementgebühren erfolgt ausschließlich über den jeweiligen App Store-Anbieter (Apple App Store für iOS-Geräte bzw. Google Play Store für Android-Geräte). Der Kunde schließt einen separaten Zahlungsvertrag mit dem jeweiligen App Store-Anbieter ab. Für die Zahlungsabwicklung gelten die Geschäftsbedingungen und Datenschutzbestimmungen von Apple Inc. (Apple Media Services – Geschäftsbedingungen) bzw. Google LLC (Google Play – Nutzungsbedingungen).',
                        paragraph_4:
                            '(4) <strong>Kündigung und Kündigungsprozess:</strong> Die Kündigung des Abonnements muss vom Kunden selbst über die Abonnementverwaltung des jeweiligen App Stores vorgenommen werden:',
                        cancellation: {
                            ios: '<strong>Apple App Store (iOS):</strong> Einstellungen → Apple-ID → Abonnements → fytrr auswählen → Abonnement kündigen',
                            android: '<strong>Google Play Store (Android):</strong> Google Play Store öffnen → Menü → Abos → fytrr auswählen → Abo kündigen',
                            note: 'Eine Kündigung über den Anbieter (fytrr) direkt ist nicht möglich. Kündigungen müssen mindestens 24 Stunden vor Ablauf der aktuellen Abrechnungsperiode erfolgen, um eine automatische Verlängerung zu verhindern.',
                        },
                        paragraph_5:
                            '(5) <strong>Wirksamwerden der Kündigung:</strong> Die Kündigung wird zum Ende der laufenden, bereits bezahlten Abrechnungsperiode wirksam. Bei Kündigung während einer laufenden Periode behält der Kunde bis zum Ende dieser Periode vollen Zugriff auf alle Premium-Funktionen der Plattform. Es erfolgt keine anteilige Rückerstattung für nicht genutzte Tage innerhalb der bezahlten Periode.',
                        paragraph_6:
                            '(6) <strong>Preisanpassungen:</strong> Der Anbieter behält sich vor, die Abonnementpreise anzupassen. Bestehende Kunden werden über Preiserhöhungen mindestens 30 Tage im Voraus informiert. Die Preiserhöhung wird erst ab der nächsten Verlängerungsperiode nach Ablauf der Informationsfrist wirksam. Der Kunde hat das Recht, das Abonnement vor Inkrafttreten der Preiserhöhung gemäß Absatz 4 zu kündigen. Preissenkungen gelten unmittelbar ab ihrer Bekanntgabe.',
                        paragraph_7:
                            '(7) <strong>Leistungsumfang Premium-Abonnement:</strong> Das Abonnement umfasst den Zugriff auf Premium-Funktionen einschließlich personalisierter KI-generierter Trainings- und Ernährungspläne, Fortschrittsverfolgung und erweiterte Analysefunktionen. Der konkrete Leistungsumfang ergibt sich aus der Beschreibung in der mobilen Applikation und kann sich im Rahmen von Updates und Weiterentwicklungen ändern.',
                        paragraph_8:
                            '(8) <strong>Anwendbare Geschäftsbedingungen der App Store-Anbieter:</strong> Zusätzlich zu diesen AGB gelten für In-App-Käufe und Abonnements die Geschäftsbedingungen des jeweiligen App Store-Anbieters. Im Falle von Widersprüchen zwischen diesen AGB und den Bedingungen der App Store-Anbieter gehen die Bedingungen der App Store-Anbieter vor, soweit dies gesetzlich zulässig ist.',
                        paragraph_9:
                            '(9) <strong>Internationale Rechtskonformität:</strong> Diese Abonnementbedingungen wurden unter Berücksichtigung der rechtlichen Anforderungen verschiedener Rechtsordnungen gestaltet:',
                        compliance: {
                            germany:
                                '<strong>Deutschland und EU:</strong> Diese Bedingungen entsprechen den Anforderungen der Datenschutz-Grundverordnung (DSGVO), des Bürgerlichen Gesetzbuchs (BGB) und des Gesetzes gegen den unlauteren Wettbewerb (UWG).',
                            uk: '<strong>Vereinigtes Königreich:</strong> Diese Bedingungen berücksichtigen den Consumer Rights Act 2015 und gewährleisten angemessene Verbraucherrechte für Kunden mit Wohnsitz im Vereinigten Königreich.',
                            us: '<strong>Vereinigte Staaten:</strong> Diese Bedingungen wurden unter Beachtung der Verbraucherschutzgesetze relevanter US-Bundesstaaten, insbesondere Kalifornien (California Consumer Privacy Act), New York und anderer Rechtsordnungen, erstellt.',
                        },
                        paragraph_10:
                            '(10) <strong>Keine anteilige Rückerstattung:</strong> Bei vorzeitiger Kündigung, Nichtinanspruchnahme der Leistungen oder Sperrung des Zugangs aufgrund eines Verstoßes gegen diese AGB erfolgt keine anteilige Rückerstattung bereits gezahlter Abonnementgebühren. Gesetzliche Ansprüche, insbesondere auf Rückabwicklung bei wirksam ausgeübtem Widerrufsrecht oder bei Mängeln der Leistung, bleiben hiervon unberührt.',
                        paragraph_11:
                            '(11) <strong>Widerrufsrecht bei Abonnements:</strong> Das Widerrufsrecht nach § 6 dieser AGB gilt auch für Abonnements. Verbraucher können den Abonnementvertrag innerhalb von 14 Tagen ohne Angabe von Gründen widerrufen. Das Widerrufsrecht erlischt vorzeitig, wenn der Kunde ausdrücklich zugestimmt hat, dass der Anbieter mit der Ausführung des Vertrages vor Ablauf der Widerrufsfrist beginnt, und der Kunde seine Kenntnis davon bestätigt hat, dass er durch seine Zustimmung mit Beginn der Ausführung des Vertrags sein Widerrufsrecht verliert. Die Ausübung des Widerrufsrechts erfolgt gegenüber dem Anbieter gemäß § 6 dieser AGB; eine Rückerstattung bereits geleisteter Zahlungen ist über den jeweiligen App Store-Anbieter zu beantragen.',
                    },
                    obligations: {
                        title: '§ 5 Pflichten des Kunden',
                        paragraph_1:
                            '(1) Der Kunde verpflichtet sich, wahrheitsgemäße Angaben zu machen. Falsche oder irreführende Angaben können zu fehlerhaften oder ungeeigneten Plänen führen.',
                        paragraph_2:
                            '(2) Der Kunde ist selbst verantwortlich für die Umsetzung der erstellten Pläne und muss seine körperliche Verfassung sowie eventuelle gesundheitliche Einschränkungen berücksichtigen.',
                        paragraph_3:
                            '(3) Der Kunde darf die Plattform nicht missbräuchlich nutzen oder gegen geltendes Recht verstoßen.',
                    },
                    withdrawal: {
                        title: '§ 6 Widerrufsrecht',
                        paragraph_1:
                            '(1) Verbrauchern steht ein gesetzliches Widerrufsrecht zu.',
                        notice: {
                            title: 'Widerrufsbelehrung',
                            right_title: 'Widerrufsrecht',
                            right_text:
                                'Sie haben das Recht, binnen vierzehn Tagen ohne Angabe von Gründen diesen Vertrag zu widerrufen.',
                            period:
                                'Die Widerrufsfrist beträgt vierzehn Tage ab dem Tag des Vertragsabschlusses.',
                            how_to:
                                'Um Ihr Widerrufsrecht auszuüben, müssen Sie uns (Tobias Lobitz, Annastraße 7, E-Mail: hello@fytrr.com) mittels einer eindeutigen Erklärung (z.B. per E-Mail) über Ihren Entschluss, diesen Vertrag zu widerrufen, informieren.',
                            consequences_title: 'Folgen des Widerrufs',
                            consequences_text:
                                'Wenn Sie diesen Vertrag widerrufen, haben wir Ihnen alle Zahlungen, die wir von Ihnen erhalten haben, unverzüglich und spätestens binnen vierzehn Tagen ab dem Tag zurückzuzahlen, an dem die Mitteilung über Ihren Widerruf dieses Vertrags bei uns eingegangen ist.',
                            expiry_title: 'Vorzeitiges Erlöschen des Widerrufsrechts',
                            expiry_text:
                                'Das Widerrufsrecht erlischt bei einem Vertrag zur Erbringung von Dienstleistungen, wenn der Unternehmer die Dienstleistung vollständig erbracht hat und mit der Ausführung erst begonnen hat, nachdem der Verbraucher dazu seine ausdrückliche Zustimmung gegeben hat und gleichzeitig seine Kenntnis davon bestätigt hat, dass er sein Widerrufsrecht bei vollständiger Vertragserfüllung verliert.',
                        },
                    },
                    liability: {
                        title: '§ 7 Haftung und Gewährleistung',
                        paragraph_1:
                            '(1) Der Anbieter haftet nach den gesetzlichen Bestimmungen für Vorsatz und grobe Fahrlässigkeit sowie für die Verletzung von Leben, Körper oder Gesundheit.',
                        paragraph_2:
                            '(2) Im Übrigen haftet der Anbieter nur bei der Verletzung wesentlicher Vertragspflichten (Kardinalpflichten). In diesem Fall ist die Haftung auf den vertragstypischen, vorhersehbaren Schaden begrenzt.',
                        paragraph_3:
                            '(3) Die Haftung nach dem Produkthaftungsgesetz bleibt unberührt.',
                        paragraph_4:
                            '(4) Eine weitergehende Haftung ist ausgeschlossen.',
                    },
                    copyright: {
                        title: '§ 8 Urheberrecht und Nutzungsrechte',
                        paragraph_1:
                            '(1) Alle durch den Anbieter erstellten Inhalte und Werke auf dieser Plattform unterliegen dem deutschen Urheberrecht.',
                        paragraph_2:
                            '(2) Die erstellten Trainings- und Ernährungspläne sind ausschließlich für den persönlichen, nicht-kommerziellen Gebrauch des Kunden bestimmt.',
                        paragraph_3:
                            '(3) Eine Weitergabe, Vervielfältigung oder kommerzielle Nutzung der Pläne ist ohne ausdrückliche Zustimmung des Anbieters nicht gestattet.',
                    },
                    data_protection: {
                        title: '§ 9 Datenschutz',
                        paragraph_1:
                            'Der Anbieter verarbeitet personenbezogene Daten des Kunden gemäß den geltenden Datenschutzbestimmungen. Einzelheiten ergeben sich aus der Datenschutzerklärung, die auf der Website einsehbar ist.',
                    },
                    final: {
                        title: '§ 10 Schlussbestimmungen',
                        paragraph_1:
                            '(1) Es gilt das Recht der Bundesrepublik Deutschland unter Ausschluss des UN-Kaufrechts.',
                        paragraph_2:
                            '(2) Ist der Kunde Kaufmann, juristische Person des öffentlichen Rechts oder öffentlich-rechtliches Sondervermögen, ist ausschließlicher Gerichtsstand für alle Streitigkeiten aus Vertragsverhältnissen zwischen dem Kunden und dem Anbieter der Sitz des Anbieters.',
                        paragraph_3:
                            '(3) Sollten einzelne Bestimmungen dieser AGB unwirksam sein oder werden, bleibt die Wirksamkeit der übrigen Bestimmungen hiervon unberührt.',
                    },
                },
                last_updated: 'Stand: Januar 2026',
            },
        },
        footer: {
            description:
                'KI-gestützte Trainings- und Ernährungspläne für deine Fitnessziele.',
            rights: 'All rights reserved.',
            copyright: '© {year} fytrr.com. Alle Rechte vorbehalten',
        },
        set_password: {
            meta: {
                title: 'Passwort festlegen - Fytrr',
            },
            title: 'Passwort festlegen',
            subtitle: 'Dein Link öffnet automatisch die Fytrr App',
            description:
                'Dein Link öffnet automatisch die Fytrr App. Falls du die App noch nicht installiert hast, lade sie unten herunter.',
            tip: {
                label: '👆 Tipp:',
                text: 'Falls sich die App nicht automatisch geöffnet hat, klicke auf den "Öffnen"-Button in deinem Browser.',
            },
            no_app: 'Hast du die App noch nicht?',
            download_prompt: 'Lade Fytrr herunter, um loszulegen',
        },
    },
} as const;

export type Locale = keyof typeof locales;
export type TranslationKeys = typeof locales.en;
